import { useId, useRef } from 'react';
import type { SummernoteEditorProps } from './SummernoteEditor.types';
import { useSummernote } from './useSummernote';
import './SummernoteEditor.base.css';
import './SummernoteEditor.plugin.css';
import './SummernoteEditor.css';

export default function SummernoteEditor(props: SummernoteEditorProps): React.JSX.Element {
  const generatedId = useId();
  const id = props.id ?? generatedId;
  const host = useRef<HTMLDivElement>(null);
  useSummernote(host, props);
  return <div id={id} ref={host} className={`summernoteEditor ${props.className ?? ''}`} style={props.style} />;
}
