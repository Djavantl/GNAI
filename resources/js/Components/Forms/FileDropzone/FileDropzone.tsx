import { forwardRef, useImperativeHandle, useRef } from 'react';
import type { FileDropzoneHandle, FileDropzoneProps } from './FileDropzone.types';
import { useFileDropzone } from './useFileDropzone';
import './FileDropzone.plugin.css';
import './FileDropzone.css';

const FileDropzone = forwardRef<FileDropzoneHandle, FileDropzoneProps>(function FileDropzone(props, forwardedRef) {
  const host = useRef<HTMLDivElement>(null);
  const instance = useFileDropzone(host, props);
  useImperativeHandle(forwardedRef, () => ({ submit: (): void => instance.current?.processQueue() }), [instance]);
  return <div ref={host} className="fileDropzone" />;
});
export default FileDropzone;
