export function json_clone<T>(variable: T): T {
  return JSON.parse(JSON.stringify(variable))
}
