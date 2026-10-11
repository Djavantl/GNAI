export type SweetAlertIconType = 'success' | 'error' | 'warning' | 'custom';

export interface SweetAlertState {
  title: string;
  text?: string;
  type?: SweetAlertIconType;
  imageUrl?: string;
  showCancelButton?: boolean;
  showConfirmButton?: boolean;
  confirmButtonText?: string;
  cancelButtonText?: string;
  confirmButtonColor?: string;
  closeOnConfirm?: boolean;
  closeOnCancel?: boolean;
  timer?: number;
  onConfirm?: () => void;
  onCancel?: () => void;
}
