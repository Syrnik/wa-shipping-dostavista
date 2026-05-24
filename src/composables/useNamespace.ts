export function useNamespace() {
  function addns(name: string | number, ns: string): string {
    return ns?.length ? `${ns}[${name}]` : String(name)
  }
  return { addns }
}
