const VALIDATION_TRANSLATIONS = {
  'The return date field must be a valid date.': 'A data de retorno deve ser uma data válida.',
  'The departure date field must be a valid date.': 'A data de saída deve ser uma data válida.',
  'The requester name field is required.': 'O nome do solicitante é obrigatório.',
  'The destination field is required.': 'O destino é obrigatório.',
  'The departure date field is required.': 'A data de saída é obrigatória.',
  'The return date field is required.': 'A data de retorno é obrigatória.',
  'The requester name field must be at least 3 characters.': 'O nome do solicitante deve ter pelo menos 3 caracteres.',
  'The destination field must be at least 2 characters.': 'O destino deve ter pelo menos 2 caracteres.',
  'The return date must be a date after or equal to departure date.': 'A data de retorno deve ser igual ou posterior à data de saída.',
  'The departure date field must be a date after or equal to today.': 'A data de saída deve ser igual ou posterior a hoje.',
  'The given data was invalid.': 'Os dados enviados são inválidos.',
  'The selected user id is invalid.': 'O usuário selecionado é inválido.',
  'The email field is required.': 'O e-mail é obrigatório.',
  'The password field is required.': 'A senha é obrigatória.',
  'The email field must be a valid email address.': 'O e-mail deve ser um endereço válido.',
  'These credentials do not match our records.': 'E-mail ou senha incorretos.',
  'Unauthenticated.': 'Sessão expirada. Faça login novamente.',
}

const FIELD_NAMES = {
  requester_name: 'nome do solicitante',
  destination: 'destino',
  departure_date: 'data de saída',
  return_date: 'data de retorno',
  email: 'e-mail',
  password: 'senha',
  user_id: 'usuário',
}

function translateValidationMessage(msg) {
  if (VALIDATION_TRANSLATIONS[msg]) return VALIDATION_TRANSLATIONS[msg]
  let translated = msg
  for (const [en, pt] of Object.entries(FIELD_NAMES)) {
    translated = translated.replace(new RegExp(en.replace('_', ' '), 'gi'), pt)
  }
  translated = translated
    .replace(/field/g, '')
    .replace(/must be/g, 'deve ser')
    .replace(/is required/g, 'é obrigatório')
    .replace(/valid date/g, 'data válida')
    .replace(/at least (\d+) characters/g, 'pelo menos $1 caracteres')
  return translated.trim()
}

export function getErrorMessage(error) {
  const status = error?.response?.status
  const data = error?.response?.data

  if (status === 422 && data?.errors) {
    const messages = Object.values(data.errors).flat()
    const translated = messages.map((m) => translateValidationMessage(m))
    return translated.join(' ')
  }

  if (status === 422 && data?.message) {
    return translateValidationMessage(data.message)
  }

  return 'Ocorreu um erro, tente novamente mais tarde.'
}
