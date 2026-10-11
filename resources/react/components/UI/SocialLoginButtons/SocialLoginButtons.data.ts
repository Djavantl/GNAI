export const socialProviders = [
  { key: 'facebook', label: 'Facebook', icon: 'fa fa-facebook' },
  { key: 'twitter', label: 'Twitter', icon: 'fa fa-twitter' },
  { key: 'googleplus', label: 'Google+', icon: 'fa fa-google-plus' },
] as const;
export type SocialProvider = typeof socialProviders[number]['key'];
