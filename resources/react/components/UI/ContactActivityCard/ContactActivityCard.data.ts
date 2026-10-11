import { profileActivity } from "@/components/UI/ActivityTimeline/ActivityTimeline.data";
import type { ActivityItem } from "@/components/UI/ActivityTimeline/ActivityTimeline.types";

export const crmActivity: ActivityItem[] = [
  ...profileActivity,
  profileActivity[3],
  profileActivity[4],
];
