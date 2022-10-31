import DotdigitalApiService from '../core/service/api/dotdigital-api.service';

const { Application } = Shopware;

const initContainer = Application.getContainer('init');

Application.addServiceProvider(
    'DotdigitalApiService',
    (container) => new DotdigitalApiService(initContainer.httpClient, container.loginService),
);
