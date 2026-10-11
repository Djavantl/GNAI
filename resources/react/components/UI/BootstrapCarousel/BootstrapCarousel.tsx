import { useEffect, useMemo, useRef, useState } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import { isInteractiveElement } from '@/utils/carouselInteraction';
import ImageDisplay from '@/components/UI/ImageDisplay/ImageDisplay';
import './BootstrapCarousel.css';

export interface BootstrapCarouselSlide {
  alt: string;
  caption?: {
    text: string;
    title: string;
  };
  src: string;
}

interface BootstrapCarouselProps {
  ariaLabel: string;
  controls?: boolean;
  interval?: number;
  slides: BootstrapCarouselSlide[];
}

type CarouselDirection = 'next' | 'prev';

export default function BootstrapCarousel({
  ariaLabel,
  controls = false,
  interval = 5000,
  slides
}: BootstrapCarouselProps): React.JSX.Element {
  const [activeIndex, setActiveIndex] = useState<number>(0);
  const [previousIndex, setPreviousIndex] = useState<number | null>(null);
  const [direction, setDirection] = useState<CarouselDirection>('next');
  const [paused, setPaused] = useState<boolean>(false);
  const [isAnimating, setIsAnimating] = useState<boolean>(false);
  const animationTimerRef = useRef<number | null>(null);
  const dragStartXRef = useRef<number | null>(null);

  const slideCount = slides.length;

  const nextIndex = useMemo((): number => (activeIndex + 1) % slideCount, [activeIndex, slideCount]);
  const previousSlideIndex = useMemo((): number => (activeIndex - 1 + slideCount) % slideCount, [activeIndex, slideCount]);

  function goTo(targetIndex: number, requestedDirection?: CarouselDirection): void {
    if (targetIndex === activeIndex || isAnimating) {
      return;
    }

    if (animationTimerRef.current !== null) {
      window.clearTimeout(animationTimerRef.current);
    }

    const inferredDirection = requestedDirection ?? (targetIndex > activeIndex ? 'next' : 'prev');
    setPreviousIndex(activeIndex);
    setDirection(inferredDirection);
    setActiveIndex(targetIndex);
    setIsAnimating(true);

    animationTimerRef.current = window.setTimeout(() => {
      setPreviousIndex(null);
      setIsAnimating(false);
      animationTimerRef.current = null;
    }, 600);
  }

  useEffect(() => {
    if (paused || isAnimating) {
      return undefined;
    }

    const timerId = window.setInterval(() => {
      goTo((activeIndex + 1) % slideCount, 'next');
    }, interval);

    return () => {
      window.clearInterval(timerId);
    };
  }, [activeIndex, interval, isAnimating, paused, slideCount]);

  useEffect(() => () => {
    if (animationTimerRef.current !== null) {
      window.clearTimeout(animationTimerRef.current);
    }
  }, []);

  return (
    <div
      className="bootstrapCarousel slide"
      aria-label={ariaLabel}
      data-active-index={activeIndex}
      onMouseEnter={() => setPaused(true)}
      onMouseLeave={() => setPaused(false)}
      onPointerDown={(event: React.PointerEvent<HTMLDivElement>) => {
        if (isInteractiveElement(event.target)) {
          return;
        }

        dragStartXRef.current = event.clientX;
        setPaused(true);
        try {
          event.currentTarget.setPointerCapture(event.pointerId);
        } catch {
          // Some synthetic or browser-translated pointer events cannot be captured.
        }
      }}
      onPointerUp={(event: React.PointerEvent<HTMLDivElement>) => {
        const dragStartX = dragStartXRef.current;
        dragStartXRef.current = null;
        setPaused(false);

        if (dragStartX === null) {
          return;
        }

        const deltaX = event.clientX - dragStartX;

        if (Math.abs(deltaX) < 40) {
          return;
        }

        if (deltaX < 0) {
          goTo(nextIndex, 'next');
          return;
        }

        goTo(previousSlideIndex, 'prev');
      }}
      onPointerCancel={() => {
        dragStartXRef.current = null;
        setPaused(false);
      }}
    >
      <ol className="bootstrapCarousel__indicators">
        {slides.map((slide: BootstrapCarouselSlide, index: number) => (
          <li
            key={`${slide.src}-indicator`}
            className={index === activeIndex ? 'active' : undefined}
            aria-label={`Slide ${index + 1}`}
            onClick={() => goTo(index)}
          />
        ))}
      </ol>

      <div className="bootstrapCarousel__inner" role="listbox">
        {slides.map((slide: BootstrapCarouselSlide, index: number) => {
          const isActive = index === activeIndex;
          const isPrevious = previousIndex === index;

          return (
            <div
              key={slide.src}
              className={joinClassNames(
                'bootstrapCarousel__item',
                isActive ? 'active' : undefined,
                isPrevious ? 'bootstrapCarousel__item--leaving' : undefined,
                isActive && previousIndex !== null ? 'bootstrapCarousel__item--entering' : undefined,
                (isActive || isPrevious) && direction === 'next' ? 'bootstrapCarousel__item--next' : undefined,
                (isActive || isPrevious) && direction === 'prev' ? 'bootstrapCarousel__item--prev' : undefined
              )}
            >
              <ImageDisplay src={slide.src} alt={slide.alt} className="bootstrapCarousel__image" />
              {slide.caption ? (
                <div className="bootstrapCarousel__caption">
                  <h3>{slide.caption.title}</h3>
                  <p>{slide.caption.text}</p>
                </div>
              ) : null}
            </div>
          );
        })}
      </div>

      {controls ? (
        <>
          <a
            href="#"
            role="button"
            className="bootstrapCarousel__control bootstrapCarousel__control--left"
            onClick={(event: React.MouseEvent<HTMLAnchorElement>) => {
              event.preventDefault();
              goTo(previousSlideIndex, 'prev');
            }}
          >
            <span aria-hidden="true" className="fa fa-angle-left" />
            <span className="bootstrapCarousel__srOnly">Anterior</span>
          </a>
          <a
            href="#"
            role="button"
            className="bootstrapCarousel__control bootstrapCarousel__control--right"
            onClick={(event: React.MouseEvent<HTMLAnchorElement>) => {
              event.preventDefault();
              goTo(nextIndex, 'next');
            }}
          >
            <span aria-hidden="true" className="fa fa-angle-right" />
            <span className="bootstrapCarousel__srOnly">Próximo</span>
          </a>
        </>
      ) : null}
    </div>
  );
}
