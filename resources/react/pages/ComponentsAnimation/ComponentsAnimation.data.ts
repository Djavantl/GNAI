import type { AnimationOptionGroup } from './ComponentsAnimation.types';

function animationOption(value: string): { label: string; value: string } {
  return { label: value, value };
}

export const animationGroups: AnimationOptionGroup[] = [
  {
    label: 'Chamadas de atenção',
    options: ['bounce', 'flash', 'pulse', 'rubberBand', 'shake', 'swing', 'tada', 'wobble', 'jello'].map(animationOption)
  },
  {
    label: 'Entradas com bounce',
    options: ['bounceIn', 'bounceInDown', 'bounceInLeft', 'bounceInRight', 'bounceInUp'].map(animationOption)
  },
  {
    label: 'Saídas com bounce',
    options: ['bounceOut', 'bounceOutDown', 'bounceOutLeft', 'bounceOutRight', 'bounceOutUp'].map(animationOption)
  },
  {
    label: 'Entradas com fade',
    options: ['fadeIn', 'fadeInDown', 'fadeInDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig'].map(animationOption)
  },
  {
    label: 'Saídas com fade',
    options: ['fadeOut', 'fadeOutDown', 'fadeOutDownBig', 'fadeOutLeft', 'fadeOutLeftBig', 'fadeOutRight', 'fadeOutRightBig', 'fadeOutUp', 'fadeOutUpBig'].map(animationOption)
  },
  {
    label: 'Flips',
    options: ['flip', 'flipInX', 'flipInY', 'flipOutX', 'flipOutY'].map(animationOption)
  },
  {
    label: 'Lightspeed',
    options: ['lightSpeedIn', 'lightSpeedOut'].map(animationOption)
  },
  {
    label: 'Entradas com rotação',
    options: ['rotateIn', 'rotateInDownLeft', 'rotateInDownRight', 'rotateInUpLeft', 'rotateInUpRight'].map(animationOption)
  },
  {
    label: 'Saídas com rotação',
    options: ['rotateOut', 'rotateOutDownLeft', 'rotateOutDownRight', 'rotateOutUpLeft', 'rotateOutUpRight'].map(animationOption)
  },
  {
    label: 'Entradas deslizantes',
    options: ['slideInUp', 'slideInDown', 'slideInLeft', 'slideInRight'].map(animationOption)
  },
  {
    label: 'Saídas deslizantes',
    options: ['slideOutUp', 'slideOutDown', 'slideOutLeft', 'slideOutRight'].map(animationOption)
  },
  {
    label: 'Entradas com zoom',
    options: ['zoomIn', 'zoomInDown', 'zoomInLeft', 'zoomInRight', 'zoomInUp'].map(animationOption)
  },
  {
    label: 'Saídas com zoom',
    options: ['zoomOut', 'zoomOutDown', 'zoomOutLeft', 'zoomOutRight', 'zoomOutUp'].map(animationOption)
  },
  {
    label: 'Especiais',
    options: ['hinge', 'rollIn', 'rollOut'].map(animationOption)
  }
];
