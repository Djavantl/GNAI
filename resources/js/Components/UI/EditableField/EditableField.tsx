import { useRef } from 'react';
import type { EditableFieldProps } from './EditableField.types';
import { useEditableField } from './useEditableField';
import './EditableField.plugin.css';
import './EditableField.base.css';
import './EditableField.css';

export default function EditableField(props: EditableFieldProps): JSX.Element {
  const host = useRef<HTMLSpanElement>(null);
  useEditableField(host, props);
  return <span className="editableField" ref={host} />;
}
