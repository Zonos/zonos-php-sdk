(async () => {
    if (Zonos && args?.apiKey && args?.storeId) {
        Zonos.init({
            zonosApiKey: args.apiKey,
            storeId: args.storeId,
            helloSettings: {
                ...(args?.anchorSelector && {anchorElementSelector: args.anchorSelector}),
                ...(args?.cartUrlPattern && {cartUrlPattern: args.cartUrlPattern}),
                ...(args?.countryOverrideBehavior && {countryOverrideBehavior: args.countryOverrideBehavior}),
                ...(args?.currencyBehavior && {currencyBehavior: args.currencyBehavior}),
                ...(args?.currencyElementSelector && {currencyElementSelector: args.currencyElementSelector}),
                ...(args?.desktopLocation && {desktopLocation: args.desktopLocation}),
                ...(args?.productAddToCartElementSelector && {productAddToCartElementSelector: args.productAddToCartElementSelector}),
                ...(args?.productDescriptionElementSelector && {productDescriptionElementSelector: args.productDescriptionElementSelector}),
                ...(args?.productPriceElementSelector && {productPriceElementSelector: args.productPriceElementSelector}),
                ...(args?.productTitleElementSelector && {productTitleElementSelector: args.productTitleElementSelector}),
                ...(args?.automaticPopUp && {onInitSuccess: () => Zonos.openHelloDialog()})
            }
        })
    } else {
        if (!Zonos) {
            console.error("There was an error loading the integration scripts");
        }

        if (!args?.apiKey || !args?.storeId) {
            console.error("There was an error reading the plugin settings");
        }
    }
})();