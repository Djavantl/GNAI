import type { MaskExample, NumericExample } from './FormMasks.types';

// Keep the legacy patterns and currency examples; translating labels does not change their formats.
export const maskColumns: MaskExample[][] = [
  [
    { id: 'isbn-1', label: 'ISBN 1', mask: '999-99-999-9999-9', hint: 'Ex.: "999-99-999-9999-9"' },
    { id: 'isbn-2', label: 'ISBN 2', mask: '999 99 999 9999 9', hint: '999 99 999 9999 9' },
    { id: 'isbn-3', label: 'ISBN 3', mask: '999/99/999/9999/9', hint: '999/99/999/9999/9' },
    { id: 'ipv4', label: 'IPv4', mask: '999.999.999.9999', hint: '192.168.110.310' },
    { id: 'ipv6', label: 'IPv6', mask: '9999:9999:9999:9:999:9999:9999:9999', hint: '4deg:1340:6547:2:540:h8je:ve73:98pd' }
  ],
  [
    { id: 'tax-id', label: 'Identificação fiscal', mask: '99-9999999', hint: '99-9999999' },
    { id: 'phone', label: 'Telefone', mask: '(999) 999-9999', hint: '(999) 999-9999' },
    { id: 'currency', label: 'Moeda', mask: '$ 999,999,999.99', hint: '$ 999,999,999.99' },
    { id: 'date', label: 'Data', mask: '99/99/9999', hint: 'dd/mm/aaaa' },
    { id: 'date-2', label: 'Data 2', mask: '99-99-9999', hint: 'dd-mm-aaaa' }
  ]
];
export const numericColumns: NumericExample[][] = [
  [
    { id: 'number-default', label: 'Padrão', format: { groupSeparator: '.', decimalSeparator: ',' }, hint: 'Ex.: "1.234.567.890.123"' },
    { id: 'number-dollar', label: 'Numérico automático ($)', format: { currencySymbol: '$ ' }, hint: 'Ex.: "$ $ 1,234,567,890,123"' },
    { id: 'number-euro', label: 'Numérico automático (€)', format: { currencySymbol: '€ ' }, hint: 'Ex.: "€ 1,234,567,890,123"' },
    { id: 'number-rupee', label: 'Numérico automático (Rs.)', format: { currencySymbol: 'Rs. ' }, hint: 'Ex.: "Rs. 1,234,567,890,123"' },
    { id: 'number-yen', label: 'Grupo de 4 dígitos (¥)', format: { currencySymbol: '¥ ', groupSize: 4 }, hint: 'Ex.: "¥ 1,2345,6789,0123"' }
  ],
  [
    { id: 'number-pound', label: 'Numérico automático (£)', format: { currencySymbol: '£ ' }, hint: 'Ex.: "£ 1,234,567,890,123"' },
    { id: 'number-percent', label: 'Numérico automático (%)', format: { currencySymbol: '%', symbolPosition: 'suffix' }, hint: 'Ex.: "11%"' },
    { id: 'number-max', label: 'Valor máximo (0 - 9999)', format: { min: 0, max: 9999 }, hint: 'Ex.: "9,999"' },
    { id: 'number-range', label: 'Intervalo de valores (-99.99 - 1000.00)', format: { min: -99.99, max: 1000 }, hint: 'Ex.: "1000 ou -99.99"' },
    { id: 'number-brackets', label: 'Valor entre parênteses', format: { groupSeparator: '.', decimalSeparator: ',', min: -9999.99, max: 0, negativeBrackets: true }, hint: 'Ex.: "(9,999.00)"' }
  ]
];
