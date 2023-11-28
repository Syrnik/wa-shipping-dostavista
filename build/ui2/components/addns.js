export default function (name, namespace) {
    return !!namespace ? '' + namespace + '[' + name + ']' : name;
}
