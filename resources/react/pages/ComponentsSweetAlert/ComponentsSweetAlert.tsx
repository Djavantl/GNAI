import { useState } from 'react';
import { sweetAlertLongText } from './ComponentsSweetAlert.data';
import { PageTitle, SweetAlertExamplesSection } from './ComponentsSweetAlert.sections';
import SweetAlert from '@/components/UI/SweetAlert/SweetAlert';
import type { SweetAlertState } from '@/components/UI/SweetAlert/SweetAlert.types';
import type { SweetAlertKind } from './ComponentsSweetAlert.types';
import './ComponentsSweetAlert.css';

const thumbsUpImageUrl = '/assets/react/plugins/sweetalert/thumbs-up.jpg';

export default function ComponentsSweetAlert(): React.JSX.Element {
  const [alert, setAlert] = useState<SweetAlertState | null>(null);

  function openResultAlert(type: 'success' | 'error', title: string, text: string): void {
    setAlert({ title, text, type });
  }

  function openExample(exampleId: SweetAlertKind): void {
    if (exampleId === 'basic') {
      setAlert({ title: "Here's a message!" });
      return;
    }

    if (exampleId === 'title') {
      setAlert({ title: "Here's a message!", text: sweetAlertLongText });
      return;
    }

    if (exampleId === 'success') {
      setAlert({ title: 'Good job!', text: sweetAlertLongText, type: 'success' });
      return;
    }

    if (exampleId === 'warning') {
      setAlert({
        title: 'Are you sure?',
        text: 'You will not be able to recover this imaginary file!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        closeOnConfirm: false,
        onConfirm: () => openResultAlert('success', 'Deleted!', 'Your imaginary file has been deleted.')
      });
      return;
    }

    if (exampleId === 'params') {
      setAlert({
        title: 'Are you sure?',
        text: 'You will not be able to recover this imaginary file!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel plx!',
        closeOnConfirm: false,
        closeOnCancel: false,
        onConfirm: () => openResultAlert('success', 'Deleted!', 'Your imaginary file has been deleted.'),
        onCancel: () => openResultAlert('error', 'Cancelled', 'Your imaginary file is safe :)')
      });
      return;
    }

    if (exampleId === 'image') {
      setAlert({ title: 'Sweet!', text: "Here's a custom image.", type: 'custom', imageUrl: thumbsUpImageUrl });
      return;
    }

    setAlert({ title: 'Auto close alert!', text: 'I will close in 2 seconds.', showConfirmButton: false, timer: 2000 });
  }

  return (
    <section className="componentsSweetAlertPage">
      <div className="componentsSweetAlertPage__container">
        <PageTitle />
        <SweetAlertExamplesSection onOpenExample={openExample} />
      </div>
      {alert ? <SweetAlert alert={alert} onClose={() => setAlert(null)} /> : null}
    </section>
  );
}
