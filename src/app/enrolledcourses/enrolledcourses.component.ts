import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { UserService } from '../services/user.service';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-enrolledcourses',
  standalone: true,
  imports: [
            CommonModule,
            NavbarComponent,
            FooterComponent,
            ScrollButtonComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
  ],
  providers: [UserService],
  templateUrl: './enrolledcourses.component.html',
  styleUrl: './enrolledcourses.component.css'
})
export class EnrolledcoursesComponent {
  userFullName:string = '';
  userID: string = '';
  userCourses:any[]= [];
  constructor(private userService:UserService){}
  ngOnInit(){
    this.userFullName = localStorage.getItem('UserName') || '';
    this.userID = localStorage.getItem('UserID') || '';
    this.courses();
  }
  courses(){
    this.userService.getUserCourses().subscribe(
      response => {
        this.userCourses = response;
      },
      error => {
        console.error('API Error:', error )
      }
      
    )
  }
}
