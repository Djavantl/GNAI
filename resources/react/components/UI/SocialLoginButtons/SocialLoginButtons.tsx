import Button from '@/components/UI/Button/Button';
import { Fragment } from 'react';
import { socialProviders, type SocialProvider } from './SocialLoginButtons.data';
import './SocialLoginButtons.css';

export default function SocialLoginButtons({ title, onSelect }: {
  title: string;
  onSelect?: (provider: SocialProvider) => void;
}): React.JSX.Element {
  return <div className="socialLoginButtons">
    <h4><b>{title}</b></h4>
    <div className="socialLoginButtons__buttons">
      {socialProviders.map((provider, index) => <Fragment key={provider.key}>
        {index > 0 ? ' ' : null}
        <Button tone={provider.key} leftIconClass={provider.icon}
          onClick={() => onSelect?.(provider.key)}>{' '}{provider.label}</Button>
      </Fragment>)}
    </div>
  </div>;
}
