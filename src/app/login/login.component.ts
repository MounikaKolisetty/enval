import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { UserService } from '../services/user.service';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            CommonModule,
            FormsModule,
            HttpClientModule,
            ReactiveFormsModule
  ],
  providers: [UserService], // Add UserService as a provider
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnInit {
  showforget = false;
  showlogin = true;
  email = ''; 
  password = '';
  invalidInputs = false;
  passwordFieldType: string = 'password'; 
  loginForm!: FormGroup;
  forgetForm!: FormGroup;
  errorMessage: string = '';
  message = '';
  showmessage = false;

  constructor(private fb:FormBuilder, private userService: UserService) {}
  ngOnInit() {
     this.loginForm = this.fb.group({ 
      email: ['', [Validators.required, Validators.email]], 
      password: ['', [Validators.required, Validators.minLength(6)]] 
    }); 
    this.forgetForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]]
    })
  }
  
  togglePasswordVisibility() {
    this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password'; 
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
             console.log('Login successful', response); 
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
}
