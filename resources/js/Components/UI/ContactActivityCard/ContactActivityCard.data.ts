import { profileActivity } from "@/Components/UI/ActivityTimeline/ActivityTimeline.data";
import type { ActivityItem } from "@/Components/UI/ActivityTimeline/ActivityTimeline.types";

export const crmActivity: ActivityItem[] = [
  ...profileActivity,
  profileActivity[3],
  profileActivity[4],
];
