import { useId, useRef } from 'react';
import type { TinyMceEditorProps } from './TinyMceEditor.types';
import { useTinyMce } from './useTinyMce';
import './TinyMceEditor.css';

export default function TinyMceEditor(props: TinyMceEditorProps): JSX.Element {
  const id = `tinymce-${useId().replace(/:/g, '')}`;
  const host = useRef<HTMLDivElement>(null);
  useTinyMce(host, props);
  return <div id={id} ref={host} className="tinyMceEditor" />;
}
