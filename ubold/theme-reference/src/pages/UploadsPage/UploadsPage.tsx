import { useRef } from 'react';
import FormPage from '@/Components/Forms/FormPage/FormPage';
import Button from '@/Components/UI/Button/Button';
import FileDropzone from '@/Components/Forms/FileDropzone/FileDropzone';
import type { FileDropzoneHandle } from '@/Components/Forms/FileDropzone/FileDropzone.types';
import './UploadsPage.css';

export default function UploadsPage(): JSX.Element {
  const dropzone = useRef<FileDropzoneHandle>(null);
  return <FormPage title="Upload múltiplo de arquivos">
    <div className="uploadsPage"><FileDropzone ref={dropzone} />
      <div className="uploadsPage__actions"><Button tone="pink" rounded onClick={() => dropzone.current?.submit()}>Enviar</Button></div>
    </div>
  </FormPage>;
}
