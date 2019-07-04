export default {
    methods: {
        addns(name, namespace) {
            if (namespace && namespace.length) {
                return '' + namespace + '[' + name + ']';
            }
            return name;
        }
    }
}