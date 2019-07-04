module.exports = function (api) {
    const presets = [
        ["@babel/preset-env", {
            targets: {
                ie: "11"
            },
            useBuiltIns: false
        }]
    ];
    const plugins = ["@babel/plugin-transform-runtime", "@babel/plugin-syntax-dynamic-import"];
    // const plugins = [];

    api.cache(false);

    return {presets,plugins};
};


