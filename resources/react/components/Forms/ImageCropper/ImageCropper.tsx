import { forwardRef, useEffect, useImperativeHandle, useRef } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';
import type {
  CropCollection,
  CropData,
  CropRuntime,
  ImageCropperHandle,
  ImageCropperProps,
} from './ImageCropper.types';
import './ImageCropper.plugin.css';

const ImageCropper = forwardRef<ImageCropperHandle, ImageCropperProps>(
  function ImageCropper(
    {
      src,
      alt = 'Imagem para recortar',
      aspectRatio = 16 / 9,
      initialData,
      preview,
      onChange,
    },
    ref,
  ) {
    const host = useRef<HTMLDivElement>(null);
    const instance = useRef<CropCollection | undefined>(undefined);
    const callback = useRef(onChange);
    callback.current = onChange;
    useImperativeHandle(
      ref,
      () => ({
        command: (method, argument) =>
          instance.current?.cropper(method, argument),
      }),
      [],
    );
    useEffect(() => {
      let disposed = false;
      let image: HTMLImageElement | undefined;
      async function initialize(): Promise<void> {
        await loadExternalScript('/assets/react/js/jquery.min.js');
        await loadExternalScript('/assets/react/plugins/cropper/cropper.min.js');
        if (disposed || !host.current) return;
        image = document.createElement('img');
        image.src = src;
        image.alt = alt;
        host.current.append(image);
        instance.current = (
          window as unknown as Window & { jQuery: CropRuntime }
        ).jQuery(image);
        instance.current.cropper({
          aspectRatio,
          data: initialData,
          preview,
          done: (data: CropData) => callback.current?.({ ...data }),
        });
      }
      void initialize().catch(console.error);
      return () => {
        disposed = true;
        instance.current?.cropper('destroy');
        instance.current = undefined;
        image?.remove();
      };
    }, [src, alt, aspectRatio, initialData, preview]);
    return <div ref={host} className="imageCropper" />;
  },
);
export default ImageCropper;
