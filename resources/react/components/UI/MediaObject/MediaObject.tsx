import type { ElementType, ReactNode } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import ImageDisplay from '@/components/UI/ImageDisplay/ImageDisplay';
import './MediaObject.css';

export type MediaObjectAlignment = 'top' | 'middle' | 'bottom';

interface MediaObjectProps {
  as?: ElementType;
  children: ReactNode;
  className?: string;
  heading: ReactNode;
  leftImageSrc?: string;
  leftImageAlt?: string;
  leftAlignment?: MediaObjectAlignment;
  nested?: ReactNode;
  rightImageSrc?: string;
  rightImageAlt?: string;
  rightAlignment?: MediaObjectAlignment;
}

function MediaAside({
  alignment = 'top',
  alt,
  position,
  src
}: {
  alignment?: MediaObjectAlignment;
  alt: string;
  position: 'left' | 'right';
  src: string;
}): React.JSX.Element {
  return (
    <div
      className={joinClassNames(
        'mediaObject__aside',
        `mediaObject__aside--${position}`,
        alignment !== 'top' ? `mediaObject__aside--${alignment}` : undefined
      )}
    >
      <a href="#" className="mediaObject__link" onClick={(event: React.MouseEvent<HTMLAnchorElement>) => event.preventDefault()}>
        <ImageDisplay src={src} alt={alt} shape="circle" className="mediaObject__image" />
      </a>
    </div>
  );
}

export default function MediaObject({
  as: Component = 'div',
  children,
  className,
  heading,
  leftImageAlt = '64x64',
  leftImageSrc,
  leftAlignment,
  nested,
  rightImageAlt = '64x64',
  rightImageSrc,
  rightAlignment
}: MediaObjectProps): React.JSX.Element {
  return (
    <Component className={joinClassNames('mediaObject', className)}>
      {leftImageSrc ? (
        <MediaAside src={leftImageSrc} alt={leftImageAlt} position="left" alignment={leftAlignment} />
      ) : null}

      <div className="mediaObject__body">
        <h4 className="mediaObject__heading">{heading}</h4>
        {children}
        {nested}
      </div>

      {rightImageSrc ? (
        <MediaAside src={rightImageSrc} alt={rightImageAlt} position="right" alignment={rightAlignment} />
      ) : null}
    </Component>
  );
}
