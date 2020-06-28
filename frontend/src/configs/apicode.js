export function apicode(key) {
  const _map = {
    USER_TOKEN_ERROR: 20006,
    SUCCESS: 1,
    ERROR: 0
  }
  return _map[key] ?? 0
}
