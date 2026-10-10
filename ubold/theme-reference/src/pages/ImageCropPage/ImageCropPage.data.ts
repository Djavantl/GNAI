import type { ButtonTone } from '@/Components/UI/Button/Button';
export const initialCrop = { x: 420, y: 50, width: 640, height: 360 };
export const cropActions: {
  id: string;
  label: string;
  tone: ButtonTone;
  method: string;
  value?: unknown;
}[] = [
  { id: 'reset', label: 'Restaurar', tone: 'warning', method: 'reset' },
  {
    id: 'reset2',
    label: 'Restaurar tudo',
    tone: 'warning',
    method: 'reset',
    value: true,
  },
  { id: 'clear', label: 'Liberar', tone: 'primary', method: 'clear' },
  { id: 'destroy', label: 'Destruir', tone: 'danger', method: 'destroy' },
  { id: 'enable', label: 'Ativar', tone: 'success', method: 'enable' },
  { id: 'disable', label: 'Desativar', tone: 'warning', method: 'disable' },
  { id: 'zoomIn', label: 'Ampliar', tone: 'info', method: 'zoom', value: 0.1 },
  {
    id: 'zoomOut',
    label: 'Reduzir',
    tone: 'info',
    method: 'zoom',
    value: -0.1,
  },
  {
    id: 'rotateLeft',
    label: 'Girar à esquerda',
    tone: 'info',
    method: 'rotate',
    value: -90,
  },
  {
    id: 'rotateRight',
    label: 'Girar à direita',
    tone: 'info',
    method: 'rotate',
    value: 90,
  },
];
