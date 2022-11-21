const ApiService = Shopware.Classes.ApiService;

class DotdigitalApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'dotdigital') {
        super(httpClient, loginService, apiEndpoint);
    }

    getAddressBooks() {
        const headers = this.getBasicHeaders();

        return this.httpClient
            .get(`${this.getApiBasePath()}/address-books`, { headers })
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }

    getDataFields() {
        const headers = this.getBasicHeaders();

        return this.httpClient
            .get(`${this.getApiBasePath()}/data-fields`, { headers })
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }

    getPrograms() {
        const headers = this.getBasicHeaders();

        return this.httpClient
            .get(`${this.getApiBasePath()}/programs`, { headers })
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }
}

export default DotdigitalApiService;// eslint-disable-line
