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

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule,
            ReactiveFormsModule,
            RouterOutlet, RouterLink, RouterLinkActive,
            HttpClientModule,
            FooterComponent,
            NavbarComponent,
            ScrollButtonComponent
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
  emailInUse = false; // Add a flag for email in use
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
  constructor(private fb: FormBuilder, private userService: UserService, private authService: AuthService) {} 
  ngOnInit() {
     this.signUpForm = this.fb.group({
       fullName: ['', [Validators.required]],
        email: ['', [Validators.required, Validators.email]], 
        password: ['', [Validators.required, Validators.minLength(6)]] 
      }); 
      this.loginForm = this.fb.group({ 
        email: ['', [Validators.required, Validators.email]], 
        password: ['', [Validators.required, Validators.minLength(6)]] 
      }); 
      this.forgetForm = this.fb.group({
        email: ['', [Validators.required, Validators.email]]
      })
      this.authService.currentNameSource.subscribe(username => this.userFullName = username);
      this.authService.currentisLoggedIn.subscribe(loggedIn => this.isLoggedIn = loggedIn);
  }
  togglePasswordVisibility() {
     this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password'; 
    }
  signUp() { 
    if (this.signUpForm.invalid) { 
      return; 
    } 
    const { fullName, email, password } = this.signUpForm.value;
    this.user = {fullName, email, password};
    this.userService.signUp(this.user).subscribe( 
      response => { 
        if (response.emailInUse) {
           this.emailInUse = true; 
          } 
        else { 
          this.emailInUse = false; 
          console.log('User signed up successfully', response); 
        } 
      }, 
      error => {
        console.error('Error signing up user', error); 
      })
  }
  toggleDiv() {
    this.showforget = !this.showforget;
    this.showlogin = !this.showlogin;
  }
  login() { 
    if (this.loginForm.invalid) { 
      return; 
    } 
    const { email, password } = this.loginForm.value;

    this.userService.login(email, password).subscribe(
       response => {
         if (response.invalidInputs) {
           this.invalidInputs = true; 
           console.log(response.message); 
          } 
          else {
             this.invalidInputs = false; 
             if (response.user) {
              const fullName = response.user.fullname; 
              this.authService.changeName(fullName);
              this.authService.changeIsLoggedin(true);
              localStorage.setItem('UserName', fullName);
            } 
          } 
      } 
    ); 
  }
  onSubmit() { 
    const email = this.forgetForm.value.email; 
    this.userService.requestPasswordReset(email).subscribe(
       response => 
        {
          this.showlogin = false;
          this.showforget = false;
          this.showmessage = true;
          console.log('Password reset email sent.', response );
        }, 
       error =>
        {
          console.log('Error sending password reset email.', error)
        } 
      );
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
  
}
