const { Directive } = Shopware;
const DDValidationRepository = new Map();

class Validator {
    /**
     * Validator constructor.
     *
     * @param identifier
     */
    constructor(
        identifier,
    ) {
        const validationGroup = Validator.getGroup(identifier);
        if (!validationGroup) {
            throw new DOMException(`Unable to find validation group ${identifier}`);
        }

        this.Types = [
            'number',
            'required',
        ];

        if (!this.Types.includes(validationGroup.config.type)) {
            throw new DOMException(`${validationGroup.config.type} is not supported by validator`);
        }

        this.identifier = identifier;
        this.vNode = validationGroup.node;
        this.expression = validationGroup.node.data.model.expression;
        this.parent = validationGroup.parent;
        this.message = validationGroup.config.message;
        this.type = validationGroup.config.type;
        this.passes = true;
    }

    /**
     * Build new validation object on vNode element
     *
     * @param element
     * @param binding
     * @param node
     * @param eventType
     */
    static build(element, binding, node, eventType = 'blur') {
        const input = element.querySelector('input');
        const messageElement = document.createElement('div');
        const identifier = `dd-validation-${input.getAttribute('name')}`;

        messageElement.setAttribute('id', identifier);
        messageElement.classList.add('sw-field__error');
        messageElement.style.visibility = 'hidden';

        element.appendChild(messageElement);

        input.addEventListener(eventType, event => Validator.handleValidationEvent(event));

        Validator.addGroup(identifier, {
            parent: element,
            node: node,
            config: binding.value,
        });
    }

    /**
     * Add group to repository
     *
     * @param identifier
     * @param data
     */
    static addGroup(identifier, data) {
        DDValidationRepository.set(identifier, data);
    }

    /**
     * Get group from repository
     *
     * @param identifier
     * @returns {any}
     */
    static getGroup(identifier) {
        return DDValidationRepository.get(identifier);
    }

    /**
     * Emit validation results
     *
     * @param data
     */
    emitValidationResults(data) {
        const handlers = (this.vNode.data && this.vNode.data.on) ||
            (this.vNode.componentOptions && this.vNode.componentOptions.listeners);

        if (handlers && handlers['dd-validation']) {
            handlers['dd-validation'].fns(data);
        }
    }

    /**
     * Hande a validation run event
     *
     * @param event
     */
    static handleValidationEvent(event, passive = false) {
        const identifier = `dd-validation-${event.target.getAttribute('name')}`;
        const validator = new Validator(identifier);
        const messageContainer = document.getElementById(identifier);

        messageContainer.style.visibility = 'hidden';
        messageContainer.innerText = '';
        validator.parent.classList.remove('has--error');

        if (!validator.validate() && !passive) {
            validator.parent.classList.add('has--error');
            messageContainer.style.visibility = 'visible';
            messageContainer.innerText = validator.message;
        }
    }

    /**
     * Valid value
     *
     * @returns {boolean}
     */
    validate() {
        const value = this.vNode.componentInstance.currentValue;
        const isNumber = Number.isNaN(Number(value));
        switch (this.type) {
            case 'number':
                this.passes = !(!value || /^\s*$/.test(value)) && !isNumber && value > 0;
                break;
            case 'required':
                this.passes = !(!value || /^\s*$/.test(value));
                break;
            default:
                break;
        }

        this.emitValidationResults({
            key: this.expression,
            error: this.message,
            passes: this.passes,
        });

        return this.passes;
    }
}

Directive.register('dd-validate', {
    /**
     * Initialize the validation once it has been inserted to the DOM.
     * @param el
     * @param binding
     * @param node
     */
    inserted: (el, binding, node) => Validator.build(el, binding, node, 'blur'),
});
