export const CAMPAIGN_ACTION = Object.freeze({// eslint-disable-line
    HANDLE: 'action.create.dotdigital_mail_sender',
    COMPONENT_NAME: 'dotdigital-flow-campaign-modal',
    LABEL: 'Send transactional email',
    ICON: 'regular-envelope',
});

export const CONTACT_ACTION = Object.freeze({// eslint-disable-line
    HANDLE: 'action.create.dotdigital_contact',
    COMPONENT_NAME: 'dotdigital-flow-contact-modal',
    LABEL: 'Add or update contacts',
    ICON: 'regular-user',
});

export const PROGRAM_ACTION = Object.freeze({// eslint-disable-line
    HANDLE: 'action.create.dotdigital_program',
    COMPONENT_NAME: 'dotdigital-flow-program-modal',
    LABEL: 'Enroll a contact in a program',
    ICON: 'default-symbol-flow',
});

export default {// eslint-disable-line
    CAMPAIGN_ACTION,
    CONTACT_ACTION,
    PROGRAM_ACTION,
};
