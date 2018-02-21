export function operatorHandle(v1, ope, v2) {
  switch (ope) {
    case '=':
      return v1 = v2
      break
    case '>':
      return v1 > v2
      break
    case '<':
      return v1 < v2
      break
    case '>=':
      return v1 >= v2
      break
    case '<=':
      return v1 <= v2
      break
  }
}