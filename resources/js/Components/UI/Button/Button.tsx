import './Button.css';
import { useState } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';

export type ButtonTone =
  | 'default'
  | 'white'
  | 'primary'
  | 'success'
  | 'info'
  | 'warning'
  | 'danger'
  | 'inverse'
  | 'purple'
  | 'pink'
  | 'link'
  | 'facebook'
  | 'twitter'
  | 'linkedin'
  | 'dribbble'
  | 'googleplus'
  | 'instagram'
  | 'pinterest'
  | 'dropbox'
  | 'flickr'
  | 'tumblr'
  | 'skype'
  | 'youtube'
  | 'github';

export type ButtonSize = 'xs' | 'sm' | 'md' | 'lg';
export type ButtonWidth = 'xs' | 'sm' | 'md' | 'lg';

interface ButtonCommonProps {
  label?: string;
  tone?: ButtonTone;
  size?: ButtonSize;
  width?: ButtonWidth;
  rounded?: boolean;
  outline?: boolean;
  block?: boolean;
  iconOnly?: boolean;
  disabled?: boolean;
  leftIconClass?: string;
  rightIconClass?: string;
  labelIconClass?: string;
  labelRight?: boolean;
  waves?: boolean;
  wavesLight?: boolean;
  className?: string;
  children?: React.ReactNode;
  'aria-label'?: string;
  'aria-expanded'?: boolean;
  'aria-haspopup'?: 'menu';
  'aria-controls'?: string;
}

type ButtonAsAnchorProps = ButtonCommonProps & {
  href: string;
  onClick?: React.MouseEventHandler<HTMLAnchorElement>;
};

type ButtonAsButtonProps = ButtonCommonProps & {
  href?: undefined;
  type?: 'button' | 'submit' | 'reset';
  onClick?: React.MouseEventHandler<HTMLButtonElement>;
};

export type ButtonProps = ButtonAsAnchorProps | ButtonAsButtonProps;

function renderButtonInner({
  label,
  children,
  leftIconClass,
  rightIconClass,
  labelIconClass,
  labelRight
}: Pick<ButtonCommonProps, 'label' | 'children' | 'leftIconClass' | 'rightIconClass' | 'labelIconClass' | 'labelRight'>): JSX.Element {
  const labelContent = children ?? (label ? <span>{label}</span> : null);
  const labelIcon = labelIconClass ? (
    <span className={joinClassNames('button__label', labelRight ? 'button__label--right' : undefined)}>
      <i className={labelIconClass} />
    </span>
  ) : null;

  return (
    <>
      {!labelRight ? labelIcon : null}
      {leftIconClass ? <i className={joinClassNames(leftIconClass, label || children ? 'm-r-5' : undefined)} /> : null}
      {labelContent}
      {labelRight ? labelIcon : null}
      {rightIconClass ? <i className={joinClassNames(rightIconClass, label || children ? 'm-l-5' : undefined)} /> : null}
    </>
  );
}

export default function Button(props: ButtonProps): JSX.Element {
  const [pressed, setPressed] = useState<boolean>(false);
  const {
    label,
    tone = 'default',
    size,
    width,
    rounded = false,
    outline = false,
    block = false,
    iconOnly = false,
    disabled = false,
    leftIconClass,
    rightIconClass,
    labelIconClass,
    labelRight = false,
    waves = true,
    wavesLight = tone !== 'white',
    className,
    children
  } = props;

  const classNames = joinClassNames(
    'button',
    `button--${tone}`,
    size ? `button--${size}` : undefined,
    width ? `button--width-${width}` : undefined,
    rounded ? 'button--rounded' : undefined,
    outline ? 'button--outline' : undefined,
    block ? 'button--block' : undefined,
    iconOnly ? 'button--iconOnly' : undefined,
    disabled ? 'button--disabled' : undefined,
    pressed && !disabled ? 'button--active' : undefined,
    waves ? 'waves-effect' : undefined,
    waves && wavesLight ? 'waves-light' : undefined,
    className
  );

  const content = renderButtonInner({
    label,
    children,
    leftIconClass,
    rightIconClass,
    labelIconClass,
    labelRight
  });

  if ('href' in props && props.href !== undefined) {
    return (
      <a
        href={props.href}
        className={classNames}
        aria-disabled={disabled}
        aria-label={props['aria-label']}
        aria-expanded={props['aria-expanded']}
        aria-haspopup={props['aria-haspopup']}
        aria-controls={props['aria-controls']}
        onClick={(event: React.MouseEvent<HTMLAnchorElement>) => {
          if (disabled) {
            event.preventDefault();
            return;
          }

          props.onClick?.(event);
        }}
        onPointerDown={() => {
          if (!disabled) {
            setPressed(true);
          }
        }}
        onPointerUp={() => setPressed(false)}
        onPointerCancel={() => setPressed(false)}
        onPointerLeave={() => setPressed(false)}
      >
        {content}
      </a>
    );
  }

  return (
    <button
      type={props.type ?? 'button'}
      disabled={disabled}
      className={classNames}
      aria-label={props['aria-label']}
      aria-expanded={props['aria-expanded']}
      aria-haspopup={props['aria-haspopup']}
      aria-controls={props['aria-controls']}
      onClick={props.onClick}
      onPointerDown={() => {
        if (!disabled) {
          setPressed(true);
        }
      }}
      onPointerUp={() => setPressed(false)}
      onPointerCancel={() => setPressed(false)}
      onPointerLeave={() => setPressed(false)}
    >
      {content}
    </button>
  );
}
