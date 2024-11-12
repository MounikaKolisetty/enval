import { Component, NgModule, OnInit } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { UserService } from '../services/user.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-signup',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            HttpClientModule,
            FormsModule,
            CommonModule,
            ReactiveFormsModule 
  ],
  providers: [UserService], // Add UserService as a provider
  templateUrl: './signup.component.html',
  styleUrl: './signup.component.css'
})
export class SignupComponent implements OnInit{
  user: any = { 
    fullName: '', 
    email: '', 
    password: '' 
  };
  signUpForm!: FormGroup;
  passwordFieldType: string = 'password'; 
  emailInUse = false; // Add a flag for email in use
  constructor(private fb: FormBuilder, private userService: UserService) {} 
  ngOnInit() {
     this.signUpForm = this.fb.group({
       fullName: ['', [Validators.required]],
        email: ['', [Validators.required, Validators.email]], 
        password: ['', [Validators.required, Validators.minLength(6)]] 
      }); 
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
  
}
