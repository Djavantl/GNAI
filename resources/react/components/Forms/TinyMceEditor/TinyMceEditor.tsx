import { useId, useRef } from 'react';
import type { TinyMceEditorProps } from './TinyMceEditor.types';
import { useTinyMce } from './useTinyMce';
import './TinyMceEditor.css';

export default function TinyMceEditor(props: TinyMceEditorProps): React.JSX.Element {
  const generatedId = `tinymce-${useId().replace(/:/g, '')}`;
  const id = props.id ?? generatedId;
  const host = useRef<HTMLDivElement>(null);
  useTinyMce(host, props);
  return <div id={id} ref={host} className="tinyMceEditor" />;
}
