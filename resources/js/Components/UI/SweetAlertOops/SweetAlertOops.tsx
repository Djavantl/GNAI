import { useEffect } from 'react';
import './SweetAlertOops.css';

interface SweetAlertOopsProps {
  message: string;
  onClose: () => void;
  title?: string;
}

export default function SweetAlertOops({ message, onClose, title = 'Ops...' }: SweetAlertOopsProps): JSX.Element {
  useEffect(() => {
    document.documentElement.classList.add('stop-scrolling');
    document.body.classList.add('stop-scrolling');

    return () => {
      document.documentElement.classList.remove('stop-scrolling');
      document.body.classList.remove('stop-scrolling');
    };
  }, []);

  return (
    <div className="sweetAlertOops__overlay" role="alertdialog" aria-modal="true" aria-labelledby="sweet-alert-oops-title">
      <div className="sweetAlertOops__dialog showSweetAlert visible" data-animation="pop">
        <div className="sweetAlertOops__icon sa-icon sa-error" style={{ display: 'block' }}>
          <span className="sweetAlertOops__xMark sa-x-mark">
            <span className="sweetAlertOops__line sweetAlertOops__line--left sa-line sa-left" />
            <span className="sweetAlertOops__line sweetAlertOops__line--right sa-line sa-right" />
          </span>
        </div>
        <h2 id="sweet-alert-oops-title">{title}</h2>
        <p>{message}</p>
        <button type="button" className="sweetAlertOops__confirm confirm" onClick={onClose}>OK</button>
      </div>
    </div>
  );
}
