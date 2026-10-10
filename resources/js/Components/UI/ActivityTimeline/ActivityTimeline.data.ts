import type { ActivityItem } from "./ActivityTimeline.types";
const quote =
  '"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "';
export const profileActivity: ActivityItem[] = [
  {
    minutes: 5,
    user: "John Doe",
    action: "enviou uma foto",
    photo: '"DSC000586.jpg"',
    emphasized: true,
  },
  { minutes: 30, user: "Lorem", action: "comentou sua publicação.", quote },
  {
    minutes: 59,
    user: "Jessi",
    action: "participou de uma reunião com",
    withUser: "John Doe",
    quote,
  },
  {
    minutes: 5,
    user: "John Doe",
    action: "enviou 2 novas fotos",
    emphasized: true,
  },
  { minutes: 30, user: "Lorem", action: "comentou sua publicação.", quote },
  {
    minutes: 59,
    user: "Jessi",
    action: "participou de uma reunião com",
    withUser: "John Doe",
    quote,
  },
];
