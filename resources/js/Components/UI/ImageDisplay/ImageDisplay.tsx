import { joinClassNames } from '@/utils/joinClassNames';
import './ImageDisplay.css';

export type ImageDisplayShape = 'rounded' | 'circle' | 'thumbnail';
export type ImageDisplaySize = 'sm' | 'md' | 'lg';

interface ImageDisplayProps {
  alt: string;
  className?: string;
  shape?: ImageDisplayShape;
  size?: ImageDisplaySize;
  src: string;
  width?: number;
}

export default function ImageDisplay({
  alt,
  className,
  shape,
  size,
  src,
  width
}: ImageDisplayProps): JSX.Element {
  return (
    <img
      src={src}
      alt={alt}
      width={width}
      draggable={false}
      className={joinClassNames(
        'imageDisplay',
        shape ? `imageDisplay--${shape}` : undefined,
        size ? `imageDisplay--${size}` : undefined,
        className
      )}
    />
  );
}
