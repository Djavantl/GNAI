import { useEffect, useRef, useState } from 'react';
import { loadExternalStylesheet } from '@/utils/loadExternalStylesheet';
import type { AnimationOptionGroup } from './ComponentsAnimation.types';

interface AnimationSandboxProps {
  groups: AnimationOptionGroup[];
}

const ANIMATION_STYLESHEET_HREF = '/assets/gnai/plugins/animate.less/animate.min.css';
const animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend'.split(' ');

export default function AnimationSandbox({ groups }: AnimationSandboxProps): JSX.Element {
  const sandboxRef = useRef<HTMLSpanElement | null>(null);
  const [selectedAnimation, setSelectedAnimation] = useState<string>(groups[0]?.options[0]?.value ?? 'bounce');

  useEffect(() => {
    void loadExternalStylesheet(ANIMATION_STYLESHEET_HREF);
  }, []);

  useEffect(() => {
    const sandbox = sandboxRef.current;

    if (!sandbox) {
      return;
    }

    const clearAnimationClasses = (): void => {
      sandbox.className = '';
    };

    animationEndEvents.forEach((eventName: string) => {
      sandbox.addEventListener(eventName, clearAnimationClasses);
    });

    return () => {
      animationEndEvents.forEach((eventName: string) => {
        sandbox.removeEventListener(eventName, clearAnimationClasses);
      });
    };
  }, []);

  function triggerAnimation(animationName = selectedAnimation): void {
    const sandbox = sandboxRef.current;

    if (!sandbox) {
      return;
    }

    sandbox.className = '';
    void sandbox.offsetWidth;
    sandbox.className = `${animationName} animated`;
  }

  function handleAnimationChange(animationName: string): void {
    setSelectedAnimation(animationName);
    triggerAnimation(animationName);
  }

  return (
    <>
      <div className="componentsAnimationPage__sandboxWrapper text-center">
        <span id="animationSandbox" ref={sandboxRef} style={{ display: 'block' }}>
          <h1>Anime-me </h1>
        </span>
      </div>

      <form className="componentsAnimationPage__form m-b-30" onSubmit={(event) => event.preventDefault()}>
        <div className="componentsAnimationPage__row">
          <div className="componentsAnimationPage__col componentsAnimationPage__col--control">
            <div className="componentsAnimationPage__inputGroup input-group">
              <select
                className="componentsAnimationPage__select form-control js--animations"
                value={selectedAnimation}
                onChange={(event) => handleAnimationChange(event.target.value)}
              >
                {groups.map((group: AnimationOptionGroup) => (
                  <optgroup label={group.label} key={group.label}>
                    {group.options.map((option) => (
                      <option value={option.value} key={option.value}>{option.label}</option>
                    ))}
                  </optgroup>
                ))}
              </select>

              <span className="componentsAnimationPage__inputGroupButton input-group-btn">
                <button
                  className="componentsAnimationPage__button btn btn-default js--triggerAnimation"
                  type="button"
                  onClick={() => triggerAnimation()}
                >
                  Anime-me !
                </button>
              </span>
            </div>
          </div>
        </div>
      </form>
    </>
  );
}
