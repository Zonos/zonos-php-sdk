(async () => {
    if (Zonos && args?.apiKey && args?.storeId) {
        Zonos.init({
            zonosApiKey: args.apiKey,
            storeId: args.storeId,
        });
    } else {
        if (!Zonos) {
            console.error("There was an error loading the integration scripts");
        }

        if (!args?.apiKey || !args?.storeId) {
            console.error("There was an error reading the plugin settings");
        }
    }
})();