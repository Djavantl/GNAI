import Button from '@/components/UI/Button/Button';
import { errorDescriptions, errorStateCopy as copy } from './ErrorState.data';
import type { ErrorCode, ErrorDescription } from './ErrorState.types';
import './ErrorState.css';

export default function ErrorState({ code, description = errorDescriptions[code], homeHref = 'index.html', homeLabel = copy.home }: {
  code: ErrorCode;
  description?: ErrorDescription;
  homeHref?: string;
  homeLabel?: string;
}): React.JSX.Element {
  return <section className="errorState" aria-label={`${copy.label} ${code}`}>
    <div className="errorState__code" aria-label={code}>
      <span className="errorState__primary">{code[0]}</span>
      {code === '400' ? <span className="errorState__pink">0</span> : <i className="ti-face-sad errorState__pink" aria-hidden="true" />}
      {code === '500' ? <i className="ti-face-sad errorState__info" aria-hidden="true" /> : <span className="errorState__info">{code[2]}</span>}
    </div>
    <h2>{description.title}</h2><br />
    {description.paragraphs.map((text) => <p key={text}>{text}</p>)}
    <br /><Button href={homeHref} label={homeLabel} className="errorState__home" />
  </section>;
}
