var config = {
    map: {
        '*' : {
            imageField:             'Netzexpert_ProductConfigurator/js/form/select-image-field',
            expressionField:        'Netzexpert_ProductConfigurator/js/form/expression-field',
            configuratorOptions:    'Netzexpert_ProductConfigurator/js/configurator-options',
            configuratorValidation: 'Netzexpert_ProductConfigurator/js/configurator-validation'/*,
            catalogAddToCart:       'Netzexpert_ProductConfigurator/js/catalog-add-to-cart'*/
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Netzexpert_ProductConfigurator/js/catalog-add-to-cart-mixin' : true
            }
        }
    }
};