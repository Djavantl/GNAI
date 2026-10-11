import type { NestableItem } from './ComponentsNestable.types';

export const listOne: NestableItem[] = [
  { id: 1 },
  {
    id: 2,
    children: [
      { id: 3 },
      { id: 4 },
      { id: 5, children: [{ id: 6 }, { id: 7 }, { id: 8 }] },
      { id: 9 },
      { id: 10 }
    ]
  }
];

export const listTwo: NestableItem[] = [
  { id: 11 },
  { id: 12 },
  { id: 13 },
  { id: 14 },
  { id: 15, children: [{ id: 16 }, { id: 17 }, { id: 18 }] }
];

export const listThree: NestableItem[] = [
  { id: 13 },
  { id: 14 },
  { id: 15, children: [{ id: 16 }, { id: 17 }, { id: 18 }] }
];
