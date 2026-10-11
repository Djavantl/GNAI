import { useCallback, useRef, useState, type CSSProperties, type KeyboardEvent } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import type { FormWizardProps, WizardNavigationState } from './FormWizard.types';
import { useWizardTransition } from './useWizardTransition';
import './FormWizard.css';

export default function FormWizard({ id, steps, orientation = 'horizontal', transition = 'slideLeft', transitionDuration = 200, labels, onStepChanging, onFinish, className, style }: FormWizardProps): React.JSX.Element {
  const rootRef = useRef<HTMLDivElement>(null);
  const navigationLocked = useRef<boolean>(false);
  const [state, setState] = useState<WizardNavigationState>({ current: 0, previous: null, visited: [0], error: false, finished: false });
  const completeTransition = useCallback((): void => {
    navigationLocked.current = false;
    setState((current) => ({ ...current, previous: null }));
  }, []);
  useWizardTransition(rootRef, state.current, state.previous, transition, transitionDuration, completeTransition);
  const text = { previous: 'Anterior', next: 'Próximo', finish: 'Concluir', navigation: 'Etapas do formulário', ...labels };

  function navigate(next: number): void {
    if (navigationLocked.current || next < 0 || next >= steps.length || next === state.current) return;
    if (onStepChanging?.(state.current, next) === false) {
      setState((current) => ({ ...current, error: true }));
      return;
    }
    navigationLocked.current = true;
    setState((current) => ({ current: next, previous: current.current, visited: Array.from(new Set([...current.visited, next])), error: false, finished: false }));
    rootRef.current?.querySelector<HTMLAnchorElement>(`[data-step-index="${next}"]`)?.focus();
  }

  function finish(): void {
    if (navigationLocked.current) return;
    const valid = onFinish?.() !== false;
    setState((current) => ({ ...current, error: !valid, finished: valid }));
  }

  function handleKeyUp(event: KeyboardEvent<HTMLDivElement>): void {
    if ((event.target as HTMLElement).matches('input, textarea, select, button')) return;
    if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
      event.preventDefault();
      navigate(state.current + (event.key === 'ArrowRight' ? 1 : -1));
    }
  }

  return <div id={id} ref={rootRef} className={joinClassNames('formWizard', orientation === 'vertical' ? 'formWizard--vertical' : undefined, className)} style={{ '--wizard-step-count': steps.length, ...style } as CSSProperties} onKeyUp={handleKeyUp}>
    <div className="formWizard__steps">
      <ul role="tablist" aria-label={text.navigation} aria-orientation={orientation}>
        {steps.map((step, index) => {
          const selected = index === state.current;
          const visited = state.visited.includes(index);
          const status = selected ? `${state.finished ? 'done ' : ''}current${state.error ? ' error' : ''}` : visited ? 'done' : 'disabled';
          return <li key={step.id} className={status}>
            <a id={`${id}-tab-${step.id}`} href={`#${id}-panel-${step.id}`} role="tab" data-step-index={index} aria-selected={selected} aria-disabled={!visited} aria-controls={`${id}-panel-${step.id}`} onClick={(event) => { event.preventDefault(); if (visited) navigate(index); }}>
              <span className="formWizard__number">{index + 1}.</span> {step.title}
            </a>
          </li>;
        })}
      </ul>
    </div>
    <div className="formWizard__content">
      {steps.map((step, index) => <section key={step.id} id={`${id}-panel-${step.id}`} role="tabpanel" aria-labelledby={`${id}-tab-${step.id}`} aria-hidden={index !== state.current} className="formWizard__panel" style={{ display: index === state.current || index === state.previous ? 'block' : 'none' }}>{step.content}</section>)}
    </div>
    <div className="formWizard__actions">
      <ul aria-label="Navegação do formulário">
        <li className={state.current === 0 ? 'disabled' : undefined}><a href="#previous" aria-disabled={state.current === 0} onClick={(event) => { event.preventDefault(); navigate(state.current - 1); }}>{text.previous}</a></li>
        {state.current < steps.length - 1
          ? <li><a href="#next" onClick={(event) => { event.preventDefault(); navigate(state.current + 1); }}>{text.next}</a></li>
          : <li><a href="#finish" onClick={(event) => { event.preventDefault(); finish(); }}>{text.finish}</a></li>}
      </ul>
    </div>
  </div>;
}
