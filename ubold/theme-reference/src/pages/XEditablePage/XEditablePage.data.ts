import type { EditableFieldProps, EditableOption } from '@/Components/UI/EditableField/EditableField.types';
export const sexOptions: EditableOption[] = [{ value: 1, text: 'Masculino', color: '#5fbeaa' }, { value: 2, text: 'Feminino', color: '#5d9cec' }];
export const editableExamples: Array<Omit<EditableFieldProps, 'id' | 'mode'> & { key: string; label: string }> = [
  { key: 'username', label: 'Campo de texto simples', title: 'Digite o nome de usuário', defaultValue: 'superuser' },
  { key: 'firstname', label: 'Texto vazio, obrigatório', title: 'Digite seu primeiro nome', placeholder: 'Obrigatório', required: true, placement: 'right' },
  { key: 'sex', label: 'Seleção local, exibição personalizada', title: 'Selecione o sexo', type: 'select', options: sexOptions },
  { key: 'status', label: 'Seleção, erro ao carregar', title: 'Selecione o status', type: 'select', defaultValue: 0, initialText: 'Ativo', sourceUrl: '/status' },
  { key: 'dob', label: 'Data combinada', title: 'Selecione a data de nascimento', type: 'combodate', defaultValue: '1984-05-15' },
  { key: 'comments', label: 'Área de texto, botões abaixo. Salve com', title: 'Digite seus comentários', type: 'textarea', defaultValue: 'usuário incrível!', placeholder: 'Seus comentários aqui...' }
];
