import { useEffect, useRef, useState } from 'react';
import { animateElementScrollTop } from '@/utils/animateElementScrollTop';
import { useCustomScrollbar } from '@/hooks/useCustomScrollbar';
import type { DashboardTwoChatCurrentUser, DashboardTwoChatMessage } from './DashboardTwoChatCard.types';

export function getCurrentTimeLabel(): string {
  return new Intl.DateTimeFormat('pt-BR', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date());
}

export function useDashboardTwoChat(initialMessages: DashboardTwoChatMessage[], currentUser?: DashboardTwoChatCurrentUser, onEmptyMessage?: () => void) {
  const [messages, setMessages] = useState<DashboardTwoChatMessage[]>(initialMessages);
  const [value, setValue] = useState<string>('');
  const shouldAnimateScrollRef = useRef<boolean>(false);
  const cancelScrollAnimationRef = useRef<(() => void) | null>(null);
  const inputRef = useRef<HTMLInputElement | null>(null);
  const scrollbar = useCustomScrollbar<HTMLUListElement>(true);
  const { scrollRef: listRef } = scrollbar;

  useEffect(() => {
    if (!listRef.current) {
      return;
    }

    const listElement = listRef.current;
    const targetScrollTop = listElement.scrollHeight;

    if (!shouldAnimateScrollRef.current) {
      listElement.scrollTop = targetScrollTop;
      return;
    }

    shouldAnimateScrollRef.current = false;

    cancelScrollAnimationRef.current?.();
    cancelScrollAnimationRef.current = animateElementScrollTop(listElement, targetScrollTop);
  }, [listRef, messages]);

  useEffect(() => {
    return () => {
      cancelScrollAnimationRef.current?.();
    };
  }, []);

  function sendMessage(): void {
    const trimmedValue = value.trim();

    if (!trimmedValue) {
      onEmptyMessage?.();
      inputRef.current?.focus();
      return;
    }

    shouldAnimateScrollRef.current = true;
    setMessages((currentMessages: DashboardTwoChatMessage[]) => [
      ...currentMessages,
      {
        id: `m-${Date.now()}`,
        author: currentUser?.author ?? 'John Deo',
        avatar: currentUser?.avatar ?? '/assets/react/images/avatar-1.jpg',
        time: currentUser?.timeFormatter ? currentUser.timeFormatter() : getCurrentTimeLabel(),
        text: trimmedValue,
        isCurrentUser: currentUser?.isCurrentUser ?? false
      }
    ]);
    setValue('');
    inputRef.current?.focus();
  }

  return {
    messages,
    value,
    setValue,
    sendMessage,
    listRef,
    inputRef,
    thumbHeight: scrollbar.thumbHeight,
    thumbTop: scrollbar.thumbTop,
    isVisible: scrollbar.isVisible
  };
}
