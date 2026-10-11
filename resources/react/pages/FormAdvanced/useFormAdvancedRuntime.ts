import { useEffect, type RefObject } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';
import { loadExternalStylesheet } from '@/utils/loadExternalStylesheet';

interface JQueryCollection {
  TouchSpin?: (options?: Record<string, unknown>) => JQueryCollection;
  data: (key: string) => unknown;
  each: (callback: (index: number, element: Element) => void) => JQueryCollection;
  filestyle?: (options?: Record<string, unknown>) => JQueryCollection;
  maxlength?: (options?: Record<string, unknown>) => JQueryCollection;
  multiSelect?: (options?: Record<string, unknown>) => JQueryCollection;
  on?: (eventName: string, handler: () => void) => JQueryCollection;
  select2?: (options?: Record<string, unknown>) => JQueryCollection;
  selectpicker?: () => JQueryCollection;
  tagsinput?: () => JQueryCollection;
}

interface JQueryRuntime {
  (element: Element | string): JQueryCollection;
  fn?: {
    TouchSpin?: unknown;
    filestyle?: unknown;
    maxlength?: unknown;
    multiSelect?: unknown;
    select2?: unknown;
    selectpicker?: unknown;
    tagsinput?: unknown;
  };
}

interface SwitcheryConstructor {
  new (element: Element, options?: Record<string, unknown>): unknown;
}

interface FormAdvancedWindow extends Window {
  Switchery?: SwitcheryConstructor;
  jQuery?: JQueryRuntime;
  $?: JQueryRuntime;
}

const formAdvancedStylesheets: string[] = [
  '/assets/react/plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css',
  '/assets/react/plugins/switchery/switchery.css',
  '/assets/react/plugins/multiselect/css/multi-select.css',
  '/assets/react/plugins/select2/select2.css',
  '/assets/react/plugins/bootstrap-select/browser/bootstrap-select.min.css',
  '/assets/react/plugins/bootstrap-touchspin/browser/jquery.bootstrap-touchspin.min.css'
];

const formAdvancedScripts: string[] = [
  '/assets/react/js/jquery.min.js',
  '/assets/react/js/bootstrap.min.js',
  '/assets/react/plugins/bootstrap-tagsinput/browser/bootstrap-tagsinput.min.js',
  '/assets/react/plugins/switchery/browser/switchery.min.js',
  '/assets/react/plugins/multiselect/js/jquery.multi-select.js',
  '/assets/react/plugins/jquery-quicksearch/jquery.quicksearch.js',
  '/assets/react/plugins/select2/select2.min.js',
  '/assets/react/plugins/bootstrap-select/browser/bootstrap-select.min.js',
  '/assets/react/plugins/bootstrap-filestyle/src/bootstrap-filestyle.min.js',
  '/assets/react/plugins/bootstrap-touchspin/browser/jquery.bootstrap-touchspin.min.js',
  '/assets/react/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js'
];

function getJQueryRuntime(): JQueryRuntime | null {
  const runtimeWindow = window as FormAdvancedWindow;
  return runtimeWindow.jQuery ?? runtimeWindow.$ ?? null;
}

function getSwitcheryRuntime(): SwitcheryConstructor | null {
  return (window as FormAdvancedWindow).Switchery ?? null;
}

async function loadFormAdvancedAssets(): Promise<void> {
  await Promise.all(formAdvancedStylesheets.map((href: string) => loadExternalStylesheet(href)));

  for (const src of formAdvancedScripts) {
    await loadExternalScript(src);
  }
}

function initializeTagsInput(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.tagsinput) {
    return;
  }

  root.querySelectorAll('[data-role="tagsinput"]').forEach((element: Element) => {
    if ((element as HTMLElement).dataset.formAdvancedReady === 'tagsinput') {
      return;
    }

    runtime(element).tagsinput?.();
    (element as HTMLElement).dataset.formAdvancedReady = 'tagsinput';
  });
}

function initializeSwitchery(root: HTMLElement): void {
  const Switchery = getSwitcheryRuntime();

  if (!Switchery) {
    return;
  }

  root.querySelectorAll('[data-plugin="switchery"]').forEach((element: Element) => {
    const input = element as HTMLInputElement;

    if (input.dataset.formAdvancedReady === 'switchery') {
      return;
    }

    new Switchery(input, {
      color: input.dataset.color,
      secondaryColor: input.dataset.secondaryColor,
      size: input.dataset.size
    });
    input.dataset.formAdvancedReady = 'switchery';
  });
}

function initializeMultiSelect(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.multiSelect) {
    return;
  }

  const defaultSelect = root.querySelector('#my_multi_select1');
  const groupedSelect = root.querySelector('#my_multi_select2');
  const searchableSelect = root.querySelector('#my_multi_select3');

  if (defaultSelect && (defaultSelect as HTMLElement).dataset.formAdvancedReady !== 'multiselect') {
    runtime(defaultSelect).multiSelect?.();
    (defaultSelect as HTMLElement).dataset.formAdvancedReady = 'multiselect';
  }

  if (groupedSelect && (groupedSelect as HTMLElement).dataset.formAdvancedReady !== 'multiselect') {
    runtime(groupedSelect).multiSelect?.({ selectableOptgroup: true });
    (groupedSelect as HTMLElement).dataset.formAdvancedReady = 'multiselect';
  }

  if (searchableSelect && (searchableSelect as HTMLElement).dataset.formAdvancedReady !== 'multiselect') {
    runtime(searchableSelect).multiSelect?.({
      selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='pesquisar...'>",
      selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='pesquisar...'>",
      afterInit(this: { $container: { attr: (name: string) => string }; $selectableUl: JQueryCollection; $selectionUl: JQueryCollection }, ms: unknown): void {
        const that = this as unknown as {
          $container: { attr: (name: string) => string };
          $selectableUl: { prev: () => { quicksearch: (selector: string) => { on: (eventName: string, handler: (event: { which: number }) => boolean | undefined) => unknown } }; focus: () => void };
          $selectionUl: { prev: () => { quicksearch: (selector: string) => { on: (eventName: string, handler: (event: { which: number }) => boolean | undefined) => unknown } }; focus: () => void };
          qs1?: { cache: () => void };
          qs2?: { cache: () => void };
        };
        const selectableSearch = that.$selectableUl.prev();
        const selectionSearch = that.$selectionUl.prev();
        const selectableSearchString = `#${that.$container.attr('id')} .ms-elem-selectable:not(.ms-selected)`;
        const selectionSearchString = `#${that.$container.attr('id')} .ms-elem-selection.ms-selected`;

        void ms;
        that.qs1 = selectableSearch.quicksearch(selectableSearchString).on('keydown', (event) => {
          if (event.which === 40) {
            that.$selectableUl.focus();
            return false;
          }

          return undefined;
        }) as { cache: () => void };
        that.qs2 = selectionSearch.quicksearch(selectionSearchString).on('keydown', (event) => {
          if (event.which === 40) {
            that.$selectionUl.focus();
            return false;
          }

          return undefined;
        }) as { cache: () => void };
      },
      afterSelect(this: { qs1?: { cache: () => void }; qs2?: { cache: () => void } }): void {
        this.qs1?.cache();
        this.qs2?.cache();
      },
      afterDeselect(this: { qs1?: { cache: () => void }; qs2?: { cache: () => void } }): void {
        this.qs1?.cache();
        this.qs2?.cache();
      }
    });
    (searchableSelect as HTMLElement).dataset.formAdvancedReady = 'multiselect';
  }
}

function initializeSelect2(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.select2) {
    return;
  }

  const select2BaseOptions = {
    dropdownCssClass: 'formAdvancedPage__select2Dropdown'
  };

  root.querySelectorAll('.select2').forEach((element: Element) => {
    if ((element as HTMLElement).dataset.formAdvancedReady === 'select2') {
      return;
    }

    runtime(element).select2?.(select2BaseOptions);
    (element as HTMLElement).dataset.formAdvancedReady = 'select2';
  });

  root.querySelectorAll('.select2-limiting').forEach((element: Element) => {
    if ((element as HTMLElement).dataset.formAdvancedReady === 'select2') {
      return;
    }

    runtime(element).select2?.({ ...select2BaseOptions, maximumSelectionLength: 2 });
    (element as HTMLElement).dataset.formAdvancedReady = 'select2';
  });
}

function initializeBootstrapSelect(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.selectpicker) {
    return;
  }

  root.querySelectorAll('.selectpicker').forEach((element: Element) => {
    if ((element as HTMLElement).dataset.formAdvancedReady === 'selectpicker') {
      return;
    }

    runtime(element).selectpicker?.();

    const picker = (element as HTMLElement).nextElementSibling;
    const styleTokens = ((element as HTMLElement).dataset.style ?? '').split(' ');

    if (styleTokens.includes('btn-white')) {
      picker?.classList.add('formAdvancedPage__selectpickerWhite');
    }

    if ((element as HTMLElement).dataset.liveSearch === 'true') {
      picker?.classList.add('formAdvancedPage__selectpickerLiveSearch');
    }

    if (styleTokens.includes('btn-custom')) {
      picker?.classList.add('formAdvancedPage__selectpickerCustom');
    }

    const shouldKeepFocusOnToggle =
      !(element as HTMLSelectElement).multiple &&
      (element as HTMLElement).dataset.liveSearch !== 'true' &&
      styleTokens.includes('btn-white');

    if (shouldKeepFocusOnToggle) {
      const focusDropdownToggle = (): void => {
        window.setTimeout(() => {
          const picker = (element as HTMLElement).nextElementSibling;
          const toggle = picker?.querySelector<HTMLButtonElement>('.dropdown-toggle');
          toggle?.focus();
        }, 0);
      };

      runtime(element).on?.('changed.bs.select', focusDropdownToggle);
      runtime(element).on?.('shown.bs.select', focusDropdownToggle);
    }

    (element as HTMLElement).dataset.formAdvancedReady = 'selectpicker';
  });
}

function initializeFileStyle(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.filestyle) {
    return;
  }

  root.querySelectorAll('input[type="file"]').forEach((element: Element) => {
    if ((element as HTMLElement).dataset.formAdvancedReady === 'filestyle') {
      return;
    }

    runtime(element).filestyle?.({ input: false });
    (element as HTMLElement).dataset.formAdvancedReady = 'filestyle';
  });
}

function initializeTouchSpin(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.TouchSpin) {
    return;
  }

  const touchSpinInputs = [
    ['.vertical-spin', { verticalbuttons: true, verticalupclass: 'ion-plus-round', verticaldownclass: 'ion-minus-round' }],
    ["input[name='demo1']", { min: 0, max: 100, step: 0.1, decimals: 2, boostat: 5, maxboostedstep: 10, postfix: '%' }],
    ["input[name='demo2']", { min: -1000000000, max: 1000000000, stepinterval: 50, maxboostedstep: 10000000, prefix: '$' }],
    ["input[name='demo3']", {}],
    ["input[name='demo3_21']", { initval: 40 }],
    ["input[name='demo3_22']", { initval: 40 }],
    ["input[name='demo5']", { prefix: 'pre', postfix: 'post' }],
    ["input[name='demo0']", {}]
  ] as const;

  touchSpinInputs.forEach(([selector, options]) => {
    root.querySelectorAll(selector).forEach((element: Element) => {
      if ((element as HTMLElement).dataset.formAdvancedReady === 'touchspin') {
        return;
      }

      runtime(element).TouchSpin?.(options);
      (element as HTMLElement).dataset.formAdvancedReady = 'touchspin';
    });
  });
}

function initializeMaxLength(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.maxlength) {
    return;
  }

  const maxlengthInputs = [
    ['input#defaultconfig', {}],
    ['input#thresholdconfig', { threshold: 20 }],
    ['input#moreoptions', { alwaysShow: true, warningClass: 'label label-success', limitReachedClass: 'label label-danger' }],
    [
      'input#alloptions',
      {
        alwaysShow: true,
        warningClass: 'label label-success',
        limitReachedClass: 'label label-danger',
        separator: ' de ',
        preText: 'Você digitou ',
        postText: ' caracteres disponíveis.',
        validate: true
      }
    ],
    ['textarea#textarea', { alwaysShow: true }],
    ['input#placement', { alwaysShow: true, placement: 'top-left' }]
  ] as const;

  maxlengthInputs.forEach(([selector, options]) => {
    const element = root.querySelector(selector);

    if (!element || (element as HTMLElement).dataset.formAdvancedReady === 'maxlength') {
      return;
    }

    runtime(element).maxlength?.(options);
    (element as HTMLElement).dataset.formAdvancedReady = 'maxlength';
  });
}

export function useFormAdvancedRuntime(rootRef: RefObject<HTMLElement | null>): void {
  useEffect(() => {
    let cancelled = false;

    async function initializeAdvancedPlugins(): Promise<void> {
      await loadFormAdvancedAssets();

      if (cancelled || !rootRef.current) {
        return;
      }

      const runtime = getJQueryRuntime();

      if (!runtime) {
        return;
      }

      initializeTagsInput(rootRef.current, runtime);
      initializeSwitchery(rootRef.current);
      initializeMultiSelect(rootRef.current, runtime);
      initializeSelect2(rootRef.current, runtime);
      initializeBootstrapSelect(rootRef.current, runtime);
      initializeFileStyle(rootRef.current, runtime);
      initializeTouchSpin(rootRef.current, runtime);
      initializeMaxLength(rootRef.current, runtime);
    }

    void initializeAdvancedPlugins();

    return () => {
      cancelled = true;
    };
  }, [rootRef]);
}
