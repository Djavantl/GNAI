export interface TextAnimation {
  text: (value: string) => TextAnimation;
  fadeOut: (duration: number, onComplete: () => void) => TextAnimation;
  fadeIn: (duration: number) => TextAnimation;
  stop: (clearQueue: boolean, jumpToEnd: boolean) => TextAnimation;
}
export interface TextAnimationRuntime {
  (element: HTMLElement): TextAnimation;
}
