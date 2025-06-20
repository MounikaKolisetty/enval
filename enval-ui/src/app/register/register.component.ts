import { Component } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserService } from '../services/user.service';
import { CommonModule } from '@angular/common';
import { AuthService } from '../services/auth.service';
import { HttpClientModule } from '@angular/common/http';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { FooterComponent } from '../footer/footer.component';
import { NavbarComponent } from '../navbar/navbar.component';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { Router } from '@angular/router';
import { RecaptchaModule } from 'ng-recaptcha';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule,
            ReactiveFormsModule,
            RouterOutlet, RouterLink, RouterLinkActive,
            HttpClientModule,
            FooterComponent,
            NavbarComponent,
            ScrollButtonComponent,
            RecaptchaModule
  ],
  providers: [UserService],
  templateUrl: './register.component.html',
  styleUrl: './register.component.css'
})
export class RegisterComponent {
  signUpForm!: FormGroup;
  loginForm!: FormGroup;
  forgetForm!: FormGroup;
  user: any = { 
    fullName: '', 
    email: '', 
    password: '' 
  };
  passwordFieldType: string = 'password'; 
  error = ''; // Add a flag for email in use
  resetPassworderror = '';
  //Login
  showforget = false;
  showlogin = true;
  email = ''; 
  password = '';
  invalidInputs = false;
  errorMessage: string = '';
  message = '';
  showmessage = false;
  userFullName = '';
  isLoggedIn = false;
  notVerified = false;
  captchaResolvedLogin: string | null = null;
  captchaResolvedSignUp: string | null = null;
  response = '';
  constructor(private fb: FormBuilder, private userService: UserService, private authService: AuthService, private router: Router) {} 
  ngOnInit() {
     this.signUpForm = this.fb.group({
       fullName: ['', [Validators.required]],
        email: ['', [Validators.required, Validators.email]], 
        password: ['', [
          Validators.required,
          Validators.minLength(10),
          Validators.pattern('^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{10,}$')
        ]] 
      }); 
      this.loginForm = this.fb.group({ 
        email: ['', [Validators.required]], 
        password: ['', [Validators.required]] 
      }); 
      this.forgetForm = this.fb.group({
        email: ['', [Validators.required]]
      })
      this.authService.currentNameSource.subscribe(username => this.userFullName = username);
      this.authService.currentisLoggedIn.subscribe(loggedIn => this.isLoggedIn = loggedIn);
  }
  togglePasswordVisibility() {
     this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password'; 
    }
  signUp() { 
    this.response = 'Please hang on, signup processing'
    if (this.signUpForm.valid && this.captchaResolvedSignUp) { 
      const { fullName, email, password } = this.signUpForm.value;
      this.user = {fullName, email, password, captchaResponse: this.captchaResolvedSignUp};
      this.userService.signUp(this.user).subscribe( 
          response => {
            console.log('Success response:', response); // Log the entire response
            this.error = ''; // Clear any previous error
            if (response.message === "Verification email sent successfully.") {
              this.response = 'An email has been sent to you. Please verify your account.';
            } else {
              // Handle other successful responses if any.  Important to set this.response even in the else
              this.response = response.message;  //Display message whatever it is
              console.log("Unexpected success response", response.message);
            }
          },
          error => {
            console.error('Error response:', error); // Log the entire error object
            this.response = ''; // Clear any previous success message
            if (error.status === 403) {
              this.error = "Security check failed. Please refresh the page and try again.";
            } else if (error.status === 400) {
              // Access the response body
              try {
                const errorBody = JSON.parse(error.error); // Try to parse the error response
        
                if (errorBody && errorBody.message) {
                  this.error = errorBody.message; // Display the message from the server
                } else {
                  this.error = "Unable to create account. Please ensure all information is correct and try again later."; // Generic message as a fallback
                }
              } catch (e) {
                console.error("Error parsing error response:", e);
                this.error = "Unable to create account. Please ensure all information is correct and try again later."; // Generic message if parsing fails
              }
            } else if (error.status === 429) {
              this.error = "Too many attempts. Please try again after an hour."; // Generic server error message
            } else if (error.status === 500) {
              this.error = "An unexpected server error occurred. Please try again later."; // Generic server error message
            } else {
              this.error = "An unexpected error occurred. Please try again later.";  //Catch all
            }
          }
        );
    }
  }
  toggleDiv() {
    this.showforget = !this.showforget;
    this.showlogin = !this.showlogin;
  }
  login() { 
    if (this.loginForm.valid && this.captchaResolvedLogin) { 
    const { email, password } = this.loginForm.value;

    this.userService.login(email, password, this.captchaResolvedLogin).subscribe(
       response => {
         if(response.notVerified) {
           this.notVerified = true;
         }
         if (response.invalidInputs) {
           this.invalidInputs = true; 
           console.log(response.message); 
          } 
          else {
             this.invalidInputs = false; 
             if (response.user) {
              const fullName = response.user.username; 
              this.authService.changeName(fullName);
              this.authService.changeIsLoggedin(true);
              localStorage.setItem('UserName', fullName);
              localStorage.setItem('UserID', response.user.id);
              this.router.navigate(['enrolled-courses']);
            } 
          } 
      } 
    ); 
    }
  }
  onSubmit() {
    // Ensure the form is valid before proceeding
    if (this.forgetForm.valid) {
      const email = this.forgetForm.value.email;
      this.showlogin = false;
      this.showforget = false;
      this.showmessage = true;
      this.message = 'Hangon! Request processing'
  
      // Call the password reset service
      this.userService.requestPasswordReset(email).subscribe(
        response => {
          // Handle success response
          this.showlogin = false;
          this.showforget = false;
          this.showmessage = true;
  
          // Display a generic success message
          console.log('Password reset email sent successfully.', response);
          this.message = 'If the email is registered, a password reset link has been sent to your email address.';
        },
        error => {
          // Show the message to the user
          this.showlogin = false;
          this.showforget = false;
          this.showmessage = false;
          // Handle error response
          console.error('Error sending password reset email.', error);
  
          // Handle specific error cases based on status code
          if (error.status === 400) {
            this.resetPassworderror = 'If the email is registered, a password reset link has been sent to your email address.';
          } else if (error.status === 403) {
            this.resetPassworderror = 'Security issue detected. Please refresh the page and try again.';
          } else if (error.status === 429) {
            this.resetPassworderror = "Too many attempts. Please try again after an hour."; // Generic server error message
          } else if (error.status === 500) {
            this.resetPassworderror = 'An unexpected server error occurred. Please try again later.';
          } else {
            this.resetPassworderror = 'An unexpected error occurred. Please try again later.';
          }
  
        }
      );
    } else {
      // If the form is invalid, display an appropriate message
      console.log('Form is invalid.');
      this.message = 'Please enter a valid email address.';
      this.showmessage = true;
    }
  }
  

  showLogin(): void {
    const loginForm = document.querySelector("form.login-inner") as HTMLElement; 
    const loginText = document.querySelector(".title-text .login") as HTMLElement; 
    const signupForm = document.querySelector("form.signup-inner") as HTMLElement; 
    const signupText = document.querySelector(".title-text .signup") as HTMLElement; 
    if (signupForm && signupText) { 
      loginForm.style.marginLeft = "0%"; 
      loginText.style.marginLeft = "0%";
    } 
  } 
  showSignup(): void {
    const loginForm = document.querySelector("form.login-inner") as HTMLElement; 
    const loginText = document.querySelector(".title-text .login") as HTMLElement; 
    if (loginForm && loginText) { 
      loginForm.style.marginLeft = "-50%"; 
      loginText.style.marginLeft = "-50%"; 
    } 
  }
  showSignupFromLogin(): void{
    const signupLink: HTMLAnchorElement | null = document.querySelector(".signup a"); 
    const signupBtn: HTMLLabelElement | null = document.querySelector("label.signup");
    if (signupLink && signupBtn) {
      signupBtn.click();
    }
  }
  showLoginFromSignup(): void{
    const signupLink: HTMLAnchorElement | null = document.querySelector(".login a"); 
    const signupBtn: HTMLLabelElement | null = document.querySelector("label.login");
    if (signupLink && signupBtn) {
      signupBtn.click();
    }
  }
  resolvedLogin(captchaResponse: string | null) { 
    this.captchaResolvedLogin = captchaResponse; 
  }
  resolvedSignUp(captchaResponse: string | null) { 
    this.captchaResolvedSignUp = captchaResponse; 
  }
}
