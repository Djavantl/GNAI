import { useId } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import TextArea from "@/components/Forms/TextArea/TextArea";
import ProductFormSection from "@/components/Forms/ProductFormSection/ProductFormSection";
import { metadataFields } from "./ProductMetadataForm.data";
export default function ProductMetadataForm(): React.JSX.Element {
  const id = useId();
  return (
    <ProductFormSection title="Metadados">
      {metadataFields.map((field) => (
        <FormField
          id={`${id}-${field.name}`}
          key={field.name}
          label={field.label}
        >
          {field.rows ? (
            <TextArea
              id={`${id}-${field.name}`}
              name={field.name}
              placeholder={field.placeholder}
              rows={field.rows}
            />
          ) : (
            <TextInput
              id={`${id}-${field.name}`}
              name={field.name}
              placeholder={field.placeholder}
            />
          )}
        </FormField>
      ))}
    </ProductFormSection>
  );
}
