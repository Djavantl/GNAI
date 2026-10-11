let wavesInitialized = false;

function isWindowObject(obj: Window | null): boolean {
  return obj !== null && obj === obj.window;
}

function getWindowObject(elem: Document | Window): Window {
  return isWindowObject(elem as Window) ? (elem as Window) : (elem as Document).defaultView as Window;
}

function offset(elem: HTMLElement): { top: number; left: number } {
  const doc = elem.ownerDocument;
  const docElem = doc.documentElement;
  const box = typeof elem.getBoundingClientRect !== 'undefined' ? elem.getBoundingClientRect() : { top: 0, left: 0 };
  const win = getWindowObject(doc);

  return {
    top: box.top + win.pageYOffset - docElem.clientTop,
    left: box.left + win.pageXOffset - docElem.clientLeft
  };
}

function convertStyle(obj: Record<string, string>): string {
  return Object.entries(obj)
    .map(([key, value]) => `${key}:${value};`)
    .join('');
}

const touchHandler = {
  touches: 0,
  allowEvent(event: MouseEvent | TouchEvent): boolean {
    let allow = true;

    if (event.type === 'touchstart') {
      touchHandler.touches += 1;
    } else if (event.type === 'touchend' || event.type === 'touchcancel') {
      setTimeout(() => {
        if (touchHandler.touches > 0) {
          touchHandler.touches -= 1;
        }
      }, 500);
    } else if (event.type === 'mousedown' && touchHandler.touches > 0) {
      allow = false;
    }

    return allow;
  },
  touchup(event: MouseEvent | TouchEvent): void {
    touchHandler.allowEvent(event);
  }
};

const effect = {
  duration: 750,
  show(event: MouseEvent | TouchEvent, element: HTMLElement): void {
    if ('button' in event && event.button === 2) {
      return;
    }

    const ripple = document.createElement('div');
    ripple.className = 'waves-ripple';
    element.appendChild(ripple);

    const pos = offset(element);
    let relativeY = ('pageY' in event ? event.pageY : 0) - pos.top;
    let relativeX = ('pageX' in event ? event.pageX : 0) - pos.left;
    const scale = `scale(${(element.clientWidth / 100) * 10})`;

    if ('touches' in event && event.touches.length > 0) {
      relativeY = event.touches[0].pageY - pos.top;
      relativeX = event.touches[0].pageX - pos.left;
    }

    ripple.setAttribute('data-hold', Date.now().toString());
    ripple.setAttribute('data-scale', scale);
    ripple.setAttribute('data-x', relativeX.toString());
    ripple.setAttribute('data-y', relativeY.toString());

    let rippleStyle: Record<string, string> = {
      top: `${relativeY}px`,
      left: `${relativeX}px`
    };

    ripple.className += ' waves-notransition';
    ripple.setAttribute('style', convertStyle(rippleStyle));
    ripple.className = ripple.className.replace(' waves-notransition', '');

    rippleStyle = {
      ...rippleStyle,
      '-webkit-transform': scale,
      '-moz-transform': scale,
      '-ms-transform': scale,
      '-o-transform': scale,
      transform: scale,
      opacity: '1',
      '-webkit-transition-duration': `${effect.duration}ms`,
      '-moz-transition-duration': `${effect.duration}ms`,
      '-o-transition-duration': `${effect.duration}ms`,
      'transition-duration': `${effect.duration}ms`,
      '-webkit-transition-timing-function': 'cubic-bezier(0.250, 0.460, 0.450, 0.940)',
      '-moz-transition-timing-function': 'cubic-bezier(0.250, 0.460, 0.450, 0.940)',
      '-o-transition-timing-function': 'cubic-bezier(0.250, 0.460, 0.450, 0.940)',
      'transition-timing-function': 'cubic-bezier(0.250, 0.460, 0.450, 0.940)'
    };

    ripple.setAttribute('style', convertStyle(rippleStyle));
  },
  hide(this: HTMLElement, event: MouseEvent | TouchEvent): void {
    touchHandler.touchup(event);

    const element = this;
    const ripples = element.getElementsByClassName('waves-ripple');
    const ripple = ripples.length > 0 ? (ripples[ripples.length - 1] as HTMLElement) : null;

    if (!ripple) {
      return;
    }

    const relativeX = ripple.getAttribute('data-x');
    const relativeY = ripple.getAttribute('data-y');
    const scale = ripple.getAttribute('data-scale');
    const diff = Date.now() - Number(ripple.getAttribute('data-hold'));
    const delay = Math.max(0, 350 - diff);

    setTimeout(() => {
      const style: Record<string, string> = {
        top: `${relativeY}px`,
        left: `${relativeX}px`,
        opacity: '0',
        '-webkit-transition-duration': `${effect.duration}ms`,
        '-moz-transition-duration': `${effect.duration}ms`,
        '-o-transition-duration': `${effect.duration}ms`,
        'transition-duration': `${effect.duration}ms`,
        '-webkit-transform': scale ?? '',
        '-moz-transform': scale ?? '',
        '-ms-transform': scale ?? '',
        '-o-transform': scale ?? '',
        transform: scale ?? ''
      };

      ripple.setAttribute('style', convertStyle(style));

      setTimeout(() => {
        if (ripple.parentNode === element) {
          element.removeChild(ripple);
        }
      }, effect.duration);
    }, delay);
  }
};

function getWavesEffectElement(event: MouseEvent | TouchEvent): HTMLElement | null {
  if (touchHandler.allowEvent(event) === false) {
    return null;
  }

  let target = event.target as HTMLElement | null;

  while (target && target.parentElement !== null) {
    if (!(target instanceof SVGElement) && typeof target.className === 'string' && target.className.indexOf('waves-effect') !== -1) {
      return target;
    }

    if (target.classList?.contains('waves-effect')) {
      return target;
    }

    target = target.parentElement;
  }

  return null;
}

function showEffect(event: MouseEvent | TouchEvent): void {
  const element = getWavesEffectElement(event);

  if (!element) {
    return;
  }

  effect.show(event, element);

  if ('ontouchstart' in window) {
    element.addEventListener('touchend', effect.hide as EventListener, false);
    element.addEventListener('touchcancel', effect.hide as EventListener, false);
  }

  element.addEventListener('mouseup', effect.hide as EventListener, false);
  element.addEventListener('mouseleave', effect.hide as EventListener, false);
}

export function initWaves(): void {
  if (wavesInitialized || typeof document === 'undefined') {
    return;
  }

  if ('ontouchstart' in window) {
    document.body.addEventListener('touchstart', showEffect as EventListener, false);
  }

  document.body.addEventListener('mousedown', showEffect as EventListener, false);
  wavesInitialized = true;
}
