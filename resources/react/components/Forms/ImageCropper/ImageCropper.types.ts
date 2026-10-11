export interface CropData {
  x: number;
  y: number;
  width: number;
  height: number;
  rotate?: number;
}
export interface ImageCropperProps {
  src: string;
  alt?: string;
  aspectRatio?: number;
  initialData?: CropData;
  preview?: string;
  onChange?: (data: CropData) => void;
}
export interface ImageCropperHandle {
  command: (method: string, argument?: unknown) => unknown;
}
export interface CropCollection {
  cropper: (...args: unknown[]) => unknown;
}
export type CropRuntime = (element: HTMLElement) => CropCollection;
