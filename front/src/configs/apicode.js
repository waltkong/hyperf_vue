const _map = {
  USER_TOKEN_ERROR: 20006,
  SUCCESS: 1,
  ERROR: 0
}

export function apicode(key) {
  return _map[key] ?? 0
}

export function isApiSuccess(code) {
  if (code.toString() === _map['SUCCESS'].toString()){
    return true
  } else {
    return false
  }
}
