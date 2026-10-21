import Button from '@/components/UI/Button/Button';
import { studentRecordUrl } from './Students.utils';

export default function StudentRecordReturnButton({
  studentId,
  tab,
  cancel = false,
}: {
  studentId: number;
  tab: string;
  cancel?: boolean;
}): React.JSX.Element {
  const label = cancel ? 'Cancelar' : 'Voltar';

  return (
    <Button
      href={studentRecordUrl(studentId, tab)}
      tone="cancel"
      label={label}
      title={`${label} para o prontuário do aluno`}
      aria-label={`${label} para o prontuário do aluno`}
    />
  );
}
