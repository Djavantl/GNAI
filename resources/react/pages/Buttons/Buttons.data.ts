export type ButtonsTone =
  | 'default'
  | 'white'
  | 'primary'
  | 'success'
  | 'info'
  | 'warning'
  | 'danger'
  | 'inverse'
  | 'purple'
  | 'pink'
  | 'link';

export interface ButtonsVariantItem {
  tone: ButtonsTone;
  label: string;
}

export interface ButtonsSocialItem {
  key:
    | 'facebook'
    | 'twitter'
    | 'linkedin'
    | 'dribbble'
    | 'googleplus'
    | 'instagram'
    | 'pinterest'
    | 'dropbox'
    | 'flickr'
    | 'tumblr'
    | 'skype'
    | 'youtube'
    | 'github';
  label: string;
  iconClass: string;
}

export const defaultButtons: ButtonsVariantItem[] = [
  { tone: 'default', label: 'Padrão' },
  { tone: 'white', label: 'Branco' },
  { tone: 'primary', label: 'Primário' },
  { tone: 'success', label: 'Sucesso' },
  { tone: 'info', label: 'Info' },
  { tone: 'warning', label: 'Aviso' },
  { tone: 'danger', label: 'Perigo' },
  { tone: 'inverse', label: 'Inverso' },
  { tone: 'purple', label: 'Roxo' },
  { tone: 'pink', label: 'Rosa' },
  { tone: 'link', label: 'Link' }
];

export const roundedButtons: ButtonsVariantItem[] = defaultButtons.filter(
  (button: ButtonsVariantItem) => button.tone !== 'white' && button.tone !== 'link'
);

export const outlineButtons: ButtonsVariantItem[] = defaultButtons.filter(
  (button: ButtonsVariantItem) => button.tone !== 'link'
);

export const socialButtons: ButtonsSocialItem[] = [
  { key: 'facebook', label: 'Facebook', iconClass: 'fa fa-facebook' },
  { key: 'twitter', label: 'Twitter', iconClass: 'fa fa-twitter' },
  { key: 'linkedin', label: 'LinkedIn', iconClass: 'fa fa-linkedin' },
  { key: 'dribbble', label: 'Dribbble', iconClass: 'fa fa-dribbble' },
  { key: 'googleplus', label: 'Google+', iconClass: 'fa fa-google-plus' },
  { key: 'instagram', label: 'Instagram', iconClass: 'fa fa-instagram' },
  { key: 'pinterest', label: 'Pinterest', iconClass: 'fa fa-pinterest' },
  { key: 'dropbox', label: 'Dropbox', iconClass: 'fa fa-dropbox' },
  { key: 'flickr', label: 'Flickr', iconClass: 'fa fa-flickr' },
  { key: 'tumblr', label: 'Tumblr', iconClass: 'fa fa-tumblr' },
  { key: 'skype', label: 'Skype', iconClass: 'fa fa-skype' },
  { key: 'youtube', label: 'YouTube', iconClass: 'fa fa-youtube' },
  { key: 'github', label: 'GitHub', iconClass: 'fa fa-github' }
];

export const dropdownMenuItems: string[] = ['Ação', 'Outra ação', 'Mais uma ação aqui', 'Link separado'];
