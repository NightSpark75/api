export function operatorHandle(v1, ope, v2) {
  v1 = Number(v1)
  v2 = Number(v2)
  switch (ope) {
    case '=':
      return v1 = v2
    case '>':
      return v1 > v2
    case '<':
      return v1 < v2
    case '>=':
      return v1 >= v2
    case '<=':
      return v1 <= v2
  }
}