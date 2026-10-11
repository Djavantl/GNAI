import { useRef } from 'react';
import FormPage from '@/components/Forms/FormPage/FormPage';
import Button from '@/components/UI/Button/Button';
import FileDropzone from '@/components/Forms/FileDropzone/FileDropzone';
import type { FileDropzoneHandle } from '@/components/Forms/FileDropzone/FileDropzone.types';
import './UploadsPage.css';

export default function UploadsPage(): React.JSX.Element {
  const dropzone = useRef<FileDropzoneHandle>(null);
  return <FormPage title="Upload múltiplo de arquivos">
    <div className="uploadsPage"><FileDropzone ref={dropzone} />
      <div className="uploadsPage__actions"><Button tone="pink" rounded onClick={() => dropzone.current?.submit()}>Enviar</Button></div>
    </div>
  </FormPage>;
}
