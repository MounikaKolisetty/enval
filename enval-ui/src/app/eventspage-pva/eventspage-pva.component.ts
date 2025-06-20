import { Component, ElementRef, ViewChild } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserService } from '../services/user.service';
import { RecaptchaModule } from 'ng-recaptcha';

@Component({
  selector: 'app-eventspage-pva',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            ScrollButtonComponent,
            CommonModule,
            ReactiveFormsModule,
            RecaptchaModule
  ],
  providers: [UserService],
  templateUrl: './eventspage-pva.component.html',
  styleUrl: './eventspage-pva.component.css'
})
export class EventspagePvaComponent {
  @ViewChild('training') trainingDiv!: ElementRef;
  isContactVisible: boolean = false; 
  userForm!: FormGroup;
  messageSent: boolean = false;
  captchaResolved: boolean = false;
  captchaResponse: string | null = null;
  constructor(private fb: FormBuilder, private userService: UserService){}
  ngOnInit(){
    this.userForm = this.fb.group({
      name: ['', Validators.required],
      designation: ['', Validators.required],
      organization: ['', Validators.required], 
      location: ['', Validators.required], 
      email: ['', Validators.required], 
      mobile: ['', Validators.required]
      })
    }
  scrollToTraining() {
    const element = this.trainingDiv.nativeElement;
    const top = element.getBoundingClientRect().top + window.pageYOffset - 100; // Adjust the offset as needed
    window.scrollTo({ top: top, behavior: 'smooth' });
  }

  toggleContact() {
    this.isContactVisible = !this.isContactVisible; 
  } 
  closeContact() {
    this.isContactVisible = false; 
  }
  resolved(captchaResponse: string | null) { 
    console.log('Captcha Response:', captchaResponse); 
    this.captchaResponse = captchaResponse;
    this.captchaResolved = !!captchaResponse; // Set to true if captchaResponse is not empty 
  }
  onSubmit(){
    if(this.userForm.valid && this.captchaResolved){
      const captchaToken = this.captchaResponse ?? ''; 
    //if(this.userForm.valid){
      this.userService.sendAdvisorForm(this.userForm.value, captchaToken).subscribe(
        response => {
          console.log('Email sent successfully', response); 
          this.messageSent = true;
          this.userForm.reset();
        }, 
        error => { 
          console.error('Error sending email', error); 
        }
      );
    }
  }
}
