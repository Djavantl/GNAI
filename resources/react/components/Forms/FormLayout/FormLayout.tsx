import { forwardRef, type FormHTMLAttributes } from "react";
import { joinClassNames } from "@/utils/joinClassNames";
import "./FormLayout.css";

const FormLayout = forwardRef<
    HTMLFormElement,
    FormHTMLAttributes<HTMLFormElement>
>(function FormLayout({ className, ...props }, ref) {
    return (
        <form
            {...props}
            ref={ref}
            className={joinClassNames("formLayout", className)}
        />
    );
});

export default FormLayout;
