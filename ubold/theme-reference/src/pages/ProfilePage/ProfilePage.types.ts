import type { ProgressBarTone } from "@/Components/UI/ProgressBar/ProgressBar";
export interface ProfileData {
  name: string;
  location: string;
  avatar: string;
  details: { label: string; value: string }[];
  biography: string[];
  skills: { name: string; value: number; tone: ProgressBarTone }[];
  mapSrc: string;
}
